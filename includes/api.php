<?php
if (!class_exists('WOO_OTP_API')) {
    require(WOO_OTP_PATH . '/views/frontend/frontend.php');        
    class WOO_OTP_API
    {               
        public function __construct()
        {
            add_action('wp_ajax_apicall', array($this, 'apiCall'));
            add_action('wp_ajax_nopriv_apicall', array($this, 'apiCall'));
        }
        public function apiCall()
        {
            global $wpdb;
            $selected_otp_gateway = $wpdb->get_results("SELECT * FROM " . WOO_OTP_DB_SETTING_API . " ORDER BY id DESC LIMIT 1");
            $otp_setting_data = $wpdb->get_results("SELECT * FROM " . WOO_OTP_DB_SETTING_FAST2SMS . " ORDER BY id DESC LIMIT 1");
            $otp_setting_data_1 = $wpdb->get_results("SELECT * FROM " . WOO_OTP_DB_SETTING_TWILIO . " ORDER BY id DESC LIMIT 1");
            if ($_REQUEST['param'] == "apicall") {
                $user_number = $_REQUEST['mobile_number'];
                $user = new WOO_OTP_FRONTEND();
                $user_exists = $user->wooc_get_users_by_phone($_REQUEST['mobile_number']);
                if (!empty($user_exists)) {
                    if ($selected_otp_gateway[0]->selected_api == "Fast2Sms") {
                        //otp Length   
                        $otp_length = $this->otp_length($otp_setting_data[0]->otp_length);                             
                        //otp Resend 
                        $resend_timer = $this->otp_timer_fun($otp_setting_data[0]->otp_resend_time);                             
                        $otp = $this->generateNumericOTP(strval($otp_length));
                        $message = $this->otp_message_f($otp_setting_data[0]->otp_message,$otp);                        
                        if($_REQUEST['resend_otp'] == 0){                                                                          
                            $update_previous_request=$wpdb->update(WOO_OTP_DB, array( 'is_verified' =>  1),array('phone'=>$user_number,'is_verified'=>'0'));
                            if(!$update_previous_request){
                                echo json_encode(array('status' => -1, 'response_back' => 'Error on DB'));
                            }
                        }
                        if($_REQUEST['resend_otp'] == 1){
                            $previous_otp_encrypt = $_REQUEST['previous_otp'];  
                            $decrypt_otp = openssl_decrypt($previous_otp_encrypt,"AES-128-ECB","otp");                                
                            $update_previous=$wpdb->update(WOO_OTP_DB, array( 'is_verified' => 1),array('phone'=>$user_number,'otp'=>md5($decrypt_otp)));
                            if(!$update_previous){
                                echo json_encode(array('status' => -1,  'gateway' => 'Fast2Sms','response_back' => 'Error on DB'));
                            }
                        }
                        $result = $wpdb->insert(WOO_OTP_DB, array(
                            'phone' => $user_number,
                            'otp' =>  md5($otp),
                            'begin_time' => date("Y-m-d H:i:s"),
                            'is_verified' => '0',
                        ));
                        $fields = array(
                            "message" => $message,
                            "language" => "english",
                            "route" => "q",
                            "numbers" => $_REQUEST['mobile_number'],
                        );
                        if ($result) {
                            $response = $this->sms_api_call($fields);                            
                            $otp_encrypt = openssl_encrypt($otp,"AES-128-ECB","otp");
                            if ($response['0'] == 0) {
                                echo json_encode(array('status' => -2, 'response_back' => 'Error Occured while sending sms', 'api' => $response));
                            } else {
                                if ($_REQUEST['resend_otp'] == 0) {                                    
                                    echo json_encode(array('status' => 1, 'gateway' => 'Fast2Sms', 'response_back' => $response, 'resend_button' => '<button type="button" id="resend_otp" disabled>Resend in <span id="timer">' . $resend_timer . '</span></button>', 'resend_timer' => $resend_timer,'otp'=>$otp_encrypt));
                                }
                                if ($_REQUEST['resend_otp'] == 1) {
                                    echo json_encode(array('status' => 1, 'gateway' => 'Fast2Sms', 'response_back' => $response,'otp'=>$otp_encrypt));
                                }
                            }
                        } else {
                            echo json_encode(array('status' => -1, 'response_back' => 'Error on DB'));
                        }
                    }
                    if ($selected_otp_gateway[0]->selected_api == "Firebase") {
                        $firebase_setting = $wpdb->get_results("SELECT * FROM " . WOO_OTP_DB_SETTING_FIREBASE . " ORDER BY id DESC LIMIT 1");                        
                        $resend_timer = $this->otp_timer_fun($firebase_setting[0] ->otp_resend_timer_firebase);                             
                        if ($_REQUEST['resend_otp'] == 0) {
                            echo json_encode(array('status' => 1, 'gateway' => 'Firebase', 'response_back' => $firebase_setting[0],'resend_timer_firebase'=>$resend_timer));
                        }
                        if ($_REQUEST['resend_otp'] == 1) {
                            echo json_encode(array('status' => 1, 'gateway' => 'Firebase', 'response_back' => $firebase_setting[0]));
                        }
                    }
                    if($selected_otp_gateway[0]->selected_api == "Twilio"){
                        $otp_length = $this->otp_length($otp_setting_data_1[0]->otp_length);   
                        $resend_timer_twilio = $this->otp_timer_fun($otp_setting_data_1[0]->otp_resend_timer_twilio);      
                        $otp = $this->generateNumericOTP(strval($otp_length));
                        $message = $this->otp_message_f($otp_setting_data_1[0]->otp_message,$otp);                                            
                        if($_REQUEST['resend_otp'] == 0){                                                                          
                            $update_previous_request=$wpdb->update(WOO_OTP_DB, array( 'is_verified' =>  1),array('phone'=>$user_number,'is_verified'=>'0'));
                            if(!$update_previous_request){
                                echo json_encode(array('status' => -1, 'response_back' => 'Error on DB'));
                            }
                        }
                        if($_REQUEST['resend_otp'] == 1){
                            $previous_otp_encrypt = $_REQUEST['previous_otp'];  
                            $decrypt_otp = openssl_decrypt($previous_otp_encrypt,"AES-128-ECB","otp");                                
                            $update_previous=$wpdb->update(WOO_OTP_DB, array( 'is_verified' => 1),array('phone'=>$user_number,'otp'=>md5($decrypt_otp)));
                            if(!$update_previous){
                                echo json_encode(array('status' => -1,  'gateway' => 'Twilio','response_back' => 'Error on DB'));
                            }
                        }                        
                        $result = $wpdb->insert(WOO_OTP_DB, array(
                            'phone' => $user_number,
                            'otp' =>  md5($otp),
                            'begin_time' => date("Y-m-d H:i:s"),
                            'is_verified' => '0',
                        ));                                                      
                        $account_sid = $otp_setting_data_1[0]->account_sid;
                        $auth_token = $otp_setting_data_1[0]->authToken;
                        $twilio_reg_no = $otp_setting_data_1[0]->twilio_reg_no;
                        $country_code = $otp_setting_data_1[0]->country_code;                        
                        $gen_number = "+".$country_code.$user_number;
                        $twilio_body = [
                            'Body'=>$message,
                            'From'=>$twilio_reg_no,
                            'To'=>$gen_number,
                        ];
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL,"https://api.twilio.com/2010-04-01/Accounts/".$account_sid."/Messages.json");
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_USERPWD, "$account_sid:$auth_token");
                        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($twilio_body));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $server_output = curl_exec($ch);
                        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close ($ch);                                    
                        if ($result) {    
                            $otp_encrypt = openssl_encrypt($otp,"AES-128-ECB","otp");
                            if($responseCode >= 400){                                                                      
                                echo json_encode(array('status' => -2, 'response_back' => 'Error Occured while sending sms', 'api' => $server_output));
                            }else {
                                if ($_REQUEST['resend_otp'] == 0) {                                    
                                    echo json_encode(array('status' => 1, 'gateway' => 'Twilio', 'response_back' => $server_output, 'resend_button' => '<button type="button" id="resend_otp" disabled>Resend in <span id="timer">' . $resend_timer_twilio . '</span></button>', 'resend_timer' => $resend_timer_twilio,'otp'=>$otp_encrypt));
                                }
                                if ($_REQUEST['resend_otp'] == 1) {
                                    echo json_encode(array('status' => 1, 'gateway' => 'Twilio', 'response_back' => $server_output,'otp'=>$otp_encrypt));
                                }
                            }
                            }else {
                                echo json_encode(array('status' => -1, 'response_back' => 'Error on DB'));
                            }
                    }
                } else {
                    echo json_encode(array('status' => 0, 'response_back' => $user_exists));
                }
            }
            if ($_REQUEST['param'] == "confirm_otp") {
                $user_phone_number = $_REQUEST['mobile_number'];
                $confirm_otp = $_REQUEST['otp'];
                if (empty($user_phone_number) || empty($confirm_otp)) {
                    if (empty($user_phone_number) || empty($confirm_otp)) {
                        if (empty($user_phone_number)) {
                            echo json_encode(array('status' => -1, 'response_back' => 'Phone Number field is empty.'));
                        }
                        if (empty($confirm_otp)) {
                            echo json_encode(array('status' => -1, 'response_back' => 'Otp field is empty.'));
                        }
                        if (empty($user_phone_number) && empty($confirm_otp)) {
                            echo json_encode(array('status' => -1, 'response_back' => 'Phone Number field and Otp field is empty.'));
                        }
                    }
                }
                $user_2 = new WOO_OTP_FRONTEND();
                $user_exists_2 = $user_2->wooc_get_users_by_phone($user_phone_number);
                if (!empty($user_exists_2)) {
                    if ($selected_otp_gateway[0]->selected_api == "Fast2Sms" || $selected_otp_gateway[0]->selected_api == "Twilio" ) {
                        $confirm_otp_md = md5($confirm_otp);
                        $sql_0 = "SELECT * FROM " . WOO_OTP_DB . " WHERE phone = '$user_phone_number' AND otp= '$confirm_otp_md'";
                        $checking_0 = $wpdb->get_results($sql_0);
                        if (!empty($checking_0)) {
                            $sql_1 = "SELECT * FROM " . WOO_OTP_DB . " WHERE phone = '$user_phone_number' AND otp= '$confirm_otp_md' AND is_verified ='0'";
                            $checking = $wpdb->get_results($sql_1);
                            if (!empty($checking)) {
                                $created_date = strtotime($checking['0']->begin_time);
                                $current_date = strtotime(date("Y-m-d H:i:s"));
                                $seconds_diff  = $current_date - $created_date;
                                $difference_in_minutes = ($seconds_diff / 60);
                                if($selected_otp_gateway[0]->selected_api == "Fast2Sms"){
                                    if (!empty($otp_setting_data[0]->otp_expire_time)) {
                                        $expire_time = $otp_setting_data[0]->otp_expire_time;
                                    } else {
                                        $expire_time = 10;
                                    }
                                }
                                if($selected_otp_gateway[0]->selected_api == "Twilio"){
                                    if (!empty($otp_setting_data_1[0]->otp_expire_time)) {
                                        $expire_time = $otp_setting_data_1[0]->otp_expire_time;
                                    } else {
                                        $expire_time = 10;
                                    }
                                }
                                if ($difference_in_minutes < number_format($expire_time)) {
                                    $sql_2 = "UPDATE " . WOO_OTP_DB . " SET is_verified = replace(is_verified, '0','1') WHERE  phone = '$user_phone_number' AND otp= '$confirm_otp_md'";
                                    $verified = $wpdb->get_results($sql_2);
                                    clean_user_cache($user_exists_2['0']->ID);
                                    wp_clear_auth_cookie();
                                    wp_set_current_user($user_exists_2['0']->ID);
                                    wp_set_auth_cookie($user_exists_2['0']->ID, true, false);
                                    update_user_caches($user_exists_2);
                                    if (is_user_logged_in()) {
                                        echo json_encode(array('status' => 1, 'gateway' => 'Fast2Sms|Twilio'));
                                    }
                                } else {
                                    $sql_2 = "UPDATE " . WOO_OTP_DB . " SET is_verified = replace(is_verified, '0','-1') WHERE  phone = '$user_phone_number' AND otp= '$confirm_otp_md'";
                                    $verified = $wpdb->get_results($sql_2);
                                    echo json_encode(array('status' => -2, 'response_back' => ' OTP Expired. Please send the otp again', 'timezone' => date_default_timezone_get()));
                                }
                            } else {
                                echo json_encode(array('status' => 0, 'response_back' => 'Given Phone number with the otp is already used .'));
                            }
                        } else {
                            echo json_encode(array('status' => 0, 'response_back' => 'Given Phone number or otp is wrong.'));
                        }
                    }
                    if ($selected_otp_gateway[0]->selected_api == "Firebase") {
                        echo json_encode(array('status' => 1, 'gateway' => 'Firebase'));
                    }
                }
            }
            if ($_REQUEST['param'] == "confirm_otp_firebase") {
                $user_3 = new WOO_OTP_FRONTEND();
                $user_phone_number_1 = $_REQUEST['mobile_number'];
                $user_exists_3 = $user_3->wooc_get_users_by_phone($user_phone_number_1);
                clean_user_cache($user_exists_3['0']->ID);
                wp_clear_auth_cookie();
                wp_set_current_user($user_exists_3['0']->ID);
                wp_set_auth_cookie($user_exists_3['0']->ID, true, false);
                update_user_caches($user_exists_3);
                if (is_user_logged_in()) {
                    echo json_encode(array('status' => 1, 'gateway' => 'Firebase'));
                }
            }
            wp_die();
        }        
        public function generateNumericOTP($n)
        {
            $generator = "1357902468";
            $result = "";
            for ($i = 1; $i <= $n; $i++) {
                $result .= substr($generator, (rand() % (strlen($generator))), 1);
            }
            return $result;
        }
        public function sms_api_call($fields)
        {
            global $wpdb;
            $otp_setting_api_data = $wpdb->get_results("SELECT * FROM " . WOO_OTP_DB_SETTING_FAST2SMS . " ORDER BY id DESC LIMIT 1");
            $api_key = $otp_setting_api_data[0]->api_key;
            $curl_url = $otp_setting_api_data[0]->curl_url;
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "$curl_url",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($fields),
                CURLOPT_HTTPHEADER => array(
                    "authorization:" . $api_key,
                    "accept: */*",
                    "cache-control: no-cache",
                    "content-type: application/json"
                ),
            ));            
            $response = curl_exec($curl);
            $err = curl_error($curl);
            $arr_res = json_decode($response);
            $arr_err = json_decode($err);
            curl_close($curl);
            if ($arr_res->return == false) {
                return array('0' => 0, '1' => $arr_res, 'err' => $arr_err,'repsonse'=>$response);
            }
            if ($arr_res->return == true) {
                return array('0' => 1, '1' => $arr_res, 'err' => $arr_err,'repsonse'=>$response);
            }
        }
        public function otp_timer_fun($time){
            if (!empty($time)) {
                $raw_resend_timer = intval($time);
                if ($raw_resend_timer < 60) {
                    if ($raw_resend_timer < 10) {
                        $resend_timer = strval("0:0" . $raw_resend_timer);
                    } else {
                        $resend_timer = strval("0:" . $raw_resend_timer);
                    }
                } else {
                    $minutes = intval($raw_resend_timer / 60);
                    $seconds_raw = round(($raw_resend_timer / 60) - $minutes, 2);
                    $second_explode = explode('.', $seconds_raw);
                    if (array_key_exists("1", $second_explode)) {
                        $resend_timer = strval($minutes . ":" . $second_explode[1]);
                    } else {
                        $resend_timer = strval($minutes . ":0" . $second_explode[0]);
                    }
                }
            } else {
                $resend_timer = '0:10';
            };
            return $resend_timer;
        }
        public function otp_message_f($msg,$otp){
            if (!empty($msg)) {
                $message_dummy = $msg;
                $message = str_replace('{{otp}}', $otp, $message_dummy);
            } else {
                $message = 'Your OTP is ' . $otp;
            };  
            return $message;
        }         
        public function otp_length($length){
            //otp Length       
            if (!empty($length)) {
                $otp_length = $length;
            } else {
                $otp_length = '6';
            };           
            return $otp_length;
        }
    }
    new WOO_OTP_API();
}
