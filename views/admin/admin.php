<?php
if (!class_exists('WOO_OTP_SETTING')) {
    class WOO_OTP_SETTING
    {
        public function __construct()
        {
            add_action('admin_menu', [$this, 'admin_menu_callback']);
            add_action('wp_ajax_adminapicall', [$this, 'adminapicall']);
            add_action('wp_ajax_nopriv_adminapicall', [$this, 'adminapicall']);
        }
        public function admin_menu_callback()
        {
            add_menu_page(
                'Woo OTP Login via SMS',
                'Woo OTP Login via SMS',
                'manage_options',
                'woo-otp-login-sms-setting',
                [$this, 'setting_callback'],
                'dashicons-phone',
                59
            );
        }
        public function adminapicall()
        {
            global $wpdb;
            if ($_REQUEST['param'] == 'select_gateway') {
                $inserted = $wpdb->insert(WOO_OTP_DB_SETTING_API, [
                    'selected_api' => $_REQUEST['select_gateway'],
                ]);
                if ($inserted) {
                    echo json_encode([
                        'status' => 1,
                        'response_back' => $inserted,
                    ]);
                } else {
                    echo json_encode([
                        'status' => 0,
                        'response_back' =>
                            "<div class='notice notice-error is-dismissible'><p>" .
                            __(
                                'Something went wrong !',
                                'woo-otp-login-sms'
                            ) .
                            '</p></div>',
                    ]);
                }
            }
            if ($_REQUEST['param'] == 'otp_switch') {
                $result_otp_switch = update_option(
                    'otp_switch',
                    $_REQUEST['otp_enable']
                );
                if ($result_otp_switch) {
                    echo json_encode(['status' => 1]);
                } else {
                    echo json_encode(['status' => 0]);
                }
            }
            wp_die();
        }
        public function setting_callback()
        {
            global $wpdb;
            require WOO_OTP_PATH . 'views/admin/instruction.php';
            $instructions_obj = new WOO_OTP_INSTRUCT();
            $otp_setting_selected_gateway = $wpdb->get_results(
                'SELECT * FROM ' .
                    WOO_OTP_DB_SETTING_API .
                    ' ORDER BY id DESC LIMIT 1'
            );

            if (get_option('otp_switch') === 'on') { ?>
                <script>                
                    jQuery(document).ready(function(){                        
                        jQuery('.api_gateway_div').show();
                    });                    
                </script>
            <?php }
            if (!empty($otp_setting_selected_gateway)) {
                if (
                    $otp_setting_selected_gateway[0]->selected_api ===
                    'Fast2Sms'
                ) { ?>
                    <script>                
                        jQuery(document).ready(function(){                        
                            jQuery('.setting_form_1').show();
                            jQuery('.Fast2Sms').show();
                            jQuery('.twilio_Setting').hide();
                            jQuery('#Firebase_decision').val('No');
                            jQuery('#Twilio_decision').val('No');  
                            jQuery('#Fast2Sms_decision').val('Yes');
                        });                    
                    </script>
                    <?php }
                if (
                    $otp_setting_selected_gateway[0]->selected_api ===
                    'Firebase'
                ) { ?>
                    <script>
                        jQuery(document).ready(function(){
                            jQuery('.setting_form_1').show();
                            jQuery('.twilio_Setting').hide();
                            jQuery('.Firebase_Setting').show();     
                            jQuery('#Twilio_decision').val('No');       
                            jQuery('#Firebase_decision').val('Yes');
                            jQuery('#Fast2Sms_decision').val('No');              
                        });
                    </script>
                    <?php }
                if (
                    $otp_setting_selected_gateway[0]->selected_api === 'Twilio'
                ) { ?>
                    <script>
                        jQuery(document).ready(function(){
                            jQuery('.setting_form_1').show();
                            jQuery('.Firebase_Setting').hide(); 
                            jQuery('.twilio_Setting').show();         
                            jQuery('#Firebase_decision').val('No');
                            jQuery('#Fast2Sms_decision').val('No');              
                            jQuery('#Twilio_decision').val('Yes');  
                        });
                    </script>
                    <?php }
            }
            if (get_option('otp_switch') === 'off') { ?>
                <script>                
                    jQuery(document).ready(function(){                        
                        jQuery('.api_gateway_div,.setting_form_1').hide();
                            jQuery('#api_gateway').find("option[selected]").removeAttr("selected");
                            jQuery('#api_gateway').find("option[value='nooption']").attr("selected",true);
                    });                    
                </script>
            <?php }
            if (!empty($_POST)) {
                if ($_POST['Firebase'] == 'Yes') {
                    if (
                        !empty($_POST['api_key_firebase']) &&
                        !empty($_POST['authDomain']) &&
                        !empty($_POST['projectid']) &&
                        !empty($_POST['storage_bucket']) &&
                        !empty($_POST['messagingSenderId']) &&
                        !empty($_POST['measurementId']) &&
                        !empty($_POST['appId'])
                    ) {
                        $firebase_setting = $wpdb->insert(
                            WOO_OTP_DB_SETTING_FIREBASE,
                            [
                                'otp_resend_timer_firebase' =>
                                    $_POST['otp_resend_timer_firebase'],
                                'country_code' => $_POST['countryCode'],
                                'api_key_firebase' =>
                                    $_POST['api_key_firebase'],
                                'authDomain' => $_POST['authDomain'],
                                'projectid' => $_POST['projectid'],
                                'storage_bucket' => $_POST['storage_bucket'],
                                'messagingSenderId' =>
                                    $_POST['messagingSenderId'],
                                'appId' => $_POST['appId'],
                                'measurementId' => $_POST['measurementId'],
                            ]
                        );
                        $otp_setting_data_2 = $wpdb->get_results(
                            'SELECT * FROM ' .
                                WOO_OTP_DB_SETTING_FIREBASE .
                                ' ORDER BY id DESC LIMIT 1'
                        );
                        if ($firebase_setting) {
                            echo "<div class='notice notice-success is-dismissible'><p>" .
                                __('Setting Saved', 'woo-otp-login-sms') .
                                '</p></div>';
                        } else {
                            echo "<div class='notice notice-success is-dismissible'><p>" .
                                __(
                                    'Something Went Wrong !',
                                    'woo-otp-login-sms'
                                ) .
                                '</p></div>';
                        }
                    }
                    if (
                        empty($_POST['api_key_firebase']) ||
                        empty($_POST['authDomain']) ||
                        empty($_POST['projectid']) ||
                        empty($_POST['storage_bucket']) ||
                        empty($_POST['messagingSenderId']) ||
                        empty($_POST['measurementId']) ||
                        empty($_POST['appId'])
                    ) {
                        echo "<div class='notice notice-error is-dismissible'><p>" .
                            __(
                                'All the fields are Required !',
                                'woo-otp-login-sms'
                            ) .
                            '</p></div>';
                    }
                }
                if ($_POST['Fast2Sms'] == 'Yes') {
                    if (
                        !empty($_POST['otp_length']) &&
                        !empty($_POST['otp_message_field']) &&
                        !empty($_POST['otp_resend_timer']) &&
                        !empty($_POST['otp_expire']) &&
                        !empty($_POST['curl_url']) &&
                        !empty($_POST['api_key'])
                    ) {
                        $insert_result = $wpdb->insert(
                            WOO_OTP_DB_SETTING_FAST2SMS,
                            [
                                'otp_length' => $_POST['otp_length'],
                                'otp_message' => $_POST['otp_message_field'],
                                'otp_resend_time' => $_POST['otp_resend_timer'],
                                'otp_expire_time' => $_POST['otp_expire'],
                                'curl_url' => $_POST['curl_url'],
                                'api_key' => $_POST['api_key'],
                            ]
                        );
                        $otp_setting_data_1 = $wpdb->get_results(
                            'SELECT * FROM ' .
                                WOO_OTP_DB_SETTING_FAST2SMS .
                                ' ORDER BY id DESC LIMIT 1'
                        );
                        if ($insert_result) {
                            echo "<div class='notice notice-success is-dismissible'><p>" .
                                __('Setting Saved', 'woo-otp-login-sms') .
                                '</p></div>';
                        } else {
                            echo "<div class='notice notice-error is-dismissible'><p>" .
                                __(
                                    'Something went wrong !',
                                    'woo-otp-login-sms'
                                ) .
                                '</p></div>';
                        }
                    }
                    if (
                        empty($_POST['otp_length']) ||
                        empty($_POST['otp_message_field']) ||
                        empty($_POST['otp_resend_timer']) ||
                        empty($_POST['otp_expire']) ||
                        empty($_POST['curl_url']) ||
                        empty($_POST['api_key'])
                    ) {
                        echo "<div class='notice notice-error is-dismissible'><p>" .
                            __(
                                'All the fields are Required !',
                                'woo-otp-login-sms'
                            ) .
                            '</p></div>';
                    }
                }
                if ($_POST['Twilio'] == 'Yes') {
                    if (
                        !empty($_POST['otp_length_twilio']) &&
                        !empty($_POST['otp_message_field_twilio']) &&
                        !empty($_POST['otp_expire_twilio']) &&
                        !empty($_POST['otp_resend_timer_twilio']) &&
                        !empty($_POST['countryCode_twilio']) &&
                        !empty($_POST['twilio_register_no']) &&
                        !empty($_POST['acc_sid_twilio']) &&
                        !empty($_POST['authToken'])
                    ) {
                        $insert_result = $wpdb->insert(
                            WOO_OTP_DB_SETTING_TWILIO,
                            [
                                'otp_resend_timer_twilio' =>
                                    $_POST['otp_resend_timer_twilio'],
                                'otp_length' => $_POST['otp_length_twilio'],
                                'otp_expire_time' =>
                                    $_POST['otp_expire_twilio'],
                                'country_code' => $_POST['countryCode_twilio'],
                                'account_sid' => $_POST['acc_sid_twilio'],
                                'authToken' => $_POST['authToken'],
                                'otp_message' =>
                                    $_POST['otp_message_field_twilio'],
                                'twilio_reg_no' => $_POST['twilio_register_no'],
                            ]
                        );
                        $otp_setting_data_3 = $wpdb->get_results(
                            'SELECT * FROM ' .
                                WOO_OTP_DB_SETTING_TWILIO .
                                ' ORDER BY id DESC LIMIT 1'
                        );
                        if ($insert_result) {
                            echo "<div class='notice notice-success is-dismissible'><p>" .
                                __('Setting Saved', 'woo-otp-login-sms') .
                                '</p></div>';
                        } else {
                            echo "<div class='notice notice-error is-dismissible'><p>" .
                                __(
                                    'Something went wrong !',
                                    'woo-otp-login-sms'
                                ) .
                                '</p></div>';
                        }
                    }
                    if (
                        empty($_POST['otp_length_twilio']) ||
                        empty($_POST['otp_message_field_twilio']) ||
                        empty($_POST['otp_expire_twilio']) ||
                        empty($_POST['otp_resend_timer_twilio']) ||
                        empty($_POST['countryCode_twilio']) ||
                        empty($_POST['twilio_register_no']) ||
                        empty($_POST['acc_sid_twilio']) ||
                        empty($_POST['authToken'])
                    ) {
                        echo "<div class='notice notice-error is-dismissible'><p>" .
                            __(
                                'All the fields are Required !',
                                'woo-otp-login-sms'
                            ) .
                            '</p></div>';
                    }
                }
            }
            ?>
            <div class="wrap">
                <h1>Woo OTP Login via SMS</h1>                                    
                <div class="otp_switch">
                    <label><?php esc_html_e(
                        'Otp Functionality :',
                        'woo-otp-login-sms'
                    ); ?></label>
                    <select id="otp_enable_switch">   
                        <option value="off" <?php echo get_option(
                            'otp_switch'
                        ) === 'off'
                            ? 'selected'
                            : ''; ?> >Disabled</option>
                        <option value="on"  <?php echo get_option(
                            'otp_switch'
                        ) === 'on'
                            ? 'selected'
                            : ''; ?>  >Enabled</option>
                    </select>
                </div>
                <div class="api_gateway_div">
                    <label><?php esc_html_e(
                        'Select SMS Gateway :',
                        'woo-otp-login-sms'
                    ); ?></label>                    
                    <select id="api_gateway">
                        <option value="nooption">Select SMS Gateway </option>                                                
                        <option value="Fast2Sms" <?php echo !empty(
                            $otp_setting_selected_gateway[0]->selected_api
                        ) &&
                        $otp_setting_selected_gateway[0]->selected_api ===
                            'Fast2Sms'
                            ? 'selected'
                            : ''; ?> >Fast2Sms</option>
                        <option value="Firebase" <?php echo !empty(
                            $otp_setting_selected_gateway[0]->selected_api
                        ) &&
                        $otp_setting_selected_gateway[0]->selected_api ===
                            'Firebase'
                            ? 'selected'
                            : ''; ?>  >Firebase</option>
                        <option value="Twilio" <?php echo !empty(
                            $otp_setting_selected_gateway[0]->selected_api
                        ) &&
                        $otp_setting_selected_gateway[0]->selected_api ===
                            'Twilio'
                            ? 'selected'
                            : ''; ?>  >Twilio</option>
                    </select>
                </div>
                <form method="post" novalidate="novalidate" class="setting_form_1">                    
                    <?php
                    require WOO_OTP_PATH .
                        'views/admin/form_sections/forms.php';
                    $forms_section = new FORMS_SECTION();
                    $forms_section->fast2sms();
                    $forms_section->firebase();
                    $forms_section->twilio();
                    ?>                                                        
                </form>
            </div>
        <?php
        }
        public function sms_api_callback()
        {
            global $wpdb;
            if (!empty($_POST)) {
                if (!empty($_POST['curl_url']) && !empty($_POST['api_key'])) {
                    $insert_result = $wpdb->insert(WOO_OTP_DB_SETTING_API, [
                        'curl_url' => $_POST['curl_url'],
                        'api_key' => $_POST['api_key'],
                    ]);
                    if ($insert_result) {
                        echo "<div class='notice notice-success is-dismissible'><p>" .
                            esc_html_e(
                                'Setting Saved',
                                'woo-otp-login-sms'
                            ) .
                            '</p></div>';
                    } else {
                        echo "<div class='notice notice-error is-dismissible'><p>" .
                            esc_html_e(
                                'Something went wrong !',
                                'woo-otp-login-sms'
                            ) .
                            '</p></div>';
                    }
                }
                if (empty($_POST['curl_url']) || empty($_POST['api_key'])) {
                    echo "<div class='notice notice-error is-dismissible'><p>" .
                        esc_html_e(
                            'All the fields are required !',
                            'woo-otp-login-sms'
                        ) .
                        '</p></div>';
                }
            }
            $otp_setting_api_data = $wpdb->get_results(
                'SELECT * FROM ' .
                    WOO_OTP_DB_SETTING_API .
                    ' ORDER BY id DESC LIMIT 1'
            );?>
        <div class="wrap">
            <h1>API setup</h1>
            <form method="post" novalidate="novalidate" class="setting_form_1">  

            </form>
        </div>
        <?php
        }
    }
    new WOO_OTP_SETTING();
}
