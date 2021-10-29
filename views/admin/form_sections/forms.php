<?php 
if(!class_exists('FORMS_SECTION')){
    class FORMS_SECTION{        
        public function __construct()
        {
            require(WOO_OTP_PATH.'views/admin/instruction.php');        
            require(WOO_OTP_PATH.'views/admin/form_sections/countrycode.php');        
        }
        public function fast2sms(){ 
            global $wpdb;            
            ?>
            <div class="Fast2Sms">            
            <?php                 
                $instructions_obj= new WOO_OTP_INSTRUCT();    
                $instructions_obj->fast2sms_instruction();
                $otp_setting_data_1 = $wpdb->get_results("SELECT * FROM " . WOO_OTP_DB_SETTING_FAST2SMS . " ORDER BY id DESC LIMIT 1");
            ?>                      
            <h3>Fast2Sms OTP Setting</h3>
            <div>
                <label for="otp_length"><?php esc_html_e('Otp Length :','woo-otp-login-sms'); ?></label>                    
                <input type="text" id="otp_length" name="otp_length" placeholder="Enter the OTP Length" value="<?php echo empty($otp_setting_data_1[0]->otp_length)?'6':$otp_setting_data_1[0]->otp_length; ?>" class="setting_input_field">
            </div>
            <br>                    
            <div>
                <label class="custom_label"><?php esc_html_e('Otp Message Field :','woo-otp-login-sms'); ?></label>                    
                <textarea id="otp_message_field" name="otp_message_field" rows="4" cols="50"><?php  if(!empty($otp_setting_data_1[0]->otp_message)){ echo $otp_setting_data_1[0]->otp_message;}else{echo 'Your OTP is : {{otp}}';}?></textarea>                    
                <p><strong><?php esc_html_e('Note : ','woo-otp-login-sms'); ?></strong><?php esc_html_e('Place','woo-otp-login-sms'); ?><strong> {{otp}}</strong><?php esc_html_e(' for the otp in the message.','woo-otp-login-sms'); ?></p>
            </div>
            <br>
            <div>
                <label for="otp_resend_timer"><?php esc_html_e('Otp Resend Time :','woo-otp-login-sms'); ?></label>                   
                <input type="text" id="otp_resend_timer" name="otp_resend_timer" value="<?php echo empty($otp_setting_data_1[0]->otp_resend_time)?'10':$otp_setting_data_1[0]->otp_resend_time; ?>" class="setting_input_field" style="width:50px;"><span> Seconds</span>
            </div>
            <br>
            <div>
                <label for="otp_expire"><?php esc_html_e('Otp Expire Time :','woo-otp-login-sms'); ?></label>                   
                <input type="text" id="otp_expire" name="otp_expire" value="<?php echo empty($otp_setting_data_1[0]->otp_expire_time)?'10':$otp_setting_data_1[0]->otp_expire_time; ?>" class="setting_input_field" style="width:50px;"><span> Minutes</span>
            </div>                        
            <h3>Fast2Sms Api Configuration </h3>
            <div>
                <label for="Curl_Url"><?php esc_html_e('Curl Url :','woo-otp-login-sms'); ?></label>                    
                <input type="text" id="curl_url" name="curl_url" placeholder="Enter Curl URL " value="<?php echo empty($otp_setting_data_1[0]->curl_url)?'':$otp_setting_data_1[0]->curl_url;  ?>" class="setting_input_field_1">
            </div>
            <br>
            <div>
                <label for="Api_key"><?php  esc_html_e('Api Key :','woo-otp-login-sms'); ?></label>                    
                <input type="text" id="api_key" name="api_key" placeholder="Enter Api Key " value="<?php echo empty($otp_setting_data_1[0]->api_key)?'':$otp_setting_data_1[0]->api_key;  ?>" class="setting_input_field_1">                            
                <input type="hidden" name="Fast2Sms" id="Fast2Sms_decision" value="Yes">
            </div>                        
            <?php                     
                submit_button(); 
            ?>
        </div>            
        <?php
        }
        public function firebase(){
            global $wpdb;
            $otp_setting_data_2 = $wpdb->get_results("SELECT * FROM " . WOO_OTP_DB_SETTING_FIREBASE . " ORDER BY id DESC LIMIT 1");      
            ?>
            <div class="Firebase_Setting">
            <?php
                $instructions_obj= new WOO_OTP_INSTRUCT();    
                $instructions_obj->firebase_instruction();
            ?>
            <h3>Firebase OTP Setting</h3>
            <div>
                <label for="otp_resend_timer">Otp Resend Time :</label>                   
                <input type="text" id="otp_resend_timer" name="otp_resend_timer_firebase" value="<?php echo empty($otp_setting_data_2[0]->otp_resend_timer_firebase)?'10':$otp_setting_data_2[0]->otp_resend_timer_firebase; ?>" class="setting_input_field" style="width:50px;"><span> Seconds</span>
            </div>
            <br>  
            <div>
                <label for="otp_length"><?php esc_html_e('Country Code :','woo-otp-login-sms'); ?></label>                    
                <?php 
                    if(!empty($otp_setting_data_2[0]->country_code)){
                ?>
                    <script>   
                        jQuery(document).ready(function(){
                            jQuery("select[name='countryCode']").find("option[selected]").removeAttr("selected");
                            jQuery("select[name='countryCode']").find("option[value='<?php  echo $otp_setting_data_2[0]->country_code ;?>']").attr("selected",'selected');
                        })                                    
                    </script>
                <?php  }  

                        new COUNTRYCODE_FIELD('countryCode');
                ?>                
            </div>
            <br>                                                                                          
            <h3>Firebase Api Configuration</h3>
            <div>
                <label for="Api_key"><?php esc_html_e('Api Key :','woo-otp-login-sms'); ?></label>                    
                <input type="text" id="api_key_firebase" name="api_key_firebase" placeholder="Enter Api Key " value="<?php echo empty($otp_setting_data_2[0]->api_key_firebase)?'':$otp_setting_data_2[0]->api_key_firebase; ?>" class="setting_input_field_1">
            </div>
            <br>
            <div>
                <label for="Api_key"><?php esc_html_e('Auth Domain :','woo-otp-login-sms'); ?></label>                    
                <input type="text" id="authDomain" name="authDomain" placeholder="Enter Auth Domain" value="<?php echo empty($otp_setting_data_2[0]->authDomain)?'':$otp_setting_data_2[0]->authDomain;  ?>" class="setting_input_field_1">                                                        
            </div>                        
            <br>
            <div>
                <label for="project_id"><?php esc_html_e('Project ID :','woo-otp-login-sms'); ?></label>                    
                <input type="text" id="projectid" name="projectid" placeholder="Enter Project ID " value="<?php echo empty($otp_setting_data_2[0]->projectid)?'':$otp_setting_data_2[0]->projectid;  ?>" class="setting_input_field_1">                                                        
            </div>                        
            <br>
            <div>
                <label for="storage_bucket"><?php esc_html_e('Storage Bucket :','woo-otp-login-sms'); ?></label>                    
                <input type="text" id="storage_bucket" name="storage_bucket" placeholder="Enter Storage Bucket " value="<?php echo empty($otp_setting_data_2[0]->storage_bucket)?'':$otp_setting_data_2[0]->storage_bucket; ?>" class="setting_input_field_1">                                                    
            </div>           
            <br>             
            <div>
                <label for="messagingSenderId"><?php esc_html_e('Message Sender ID :','woo-otp-login-sms'); ?></label>                    
                <input type="text" id="messagingSenderId" name="messagingSenderId" placeholder="Enter Message Sender ID " value="<?php echo empty($otp_setting_data_2[0]->messagingSenderId)?'':$otp_setting_data_2[0]->messagingSenderId ; ?>" class="setting_input_field_1">                                                        
            </div>
            <br>             
            <div>
                <label for="appId"><?php  esc_html_e('App ID :','woo-otp-login-sms'); ?></label>                    
                <input type="text" id="appId" name="appId" placeholder="Enter App ID " value="<?php echo empty($otp_setting_data_2[0]->appId)?'':$otp_setting_data_2[0]->appId;  ?>" class="setting_input_field_1">                                                        
            </div>        
            <br>             
            <div>
                <label for="measurementId"><?php esc_html_e('Measurement ID :','woo-otp-login-sms'); ?></label>                    
                <input type="text" id="measurementId" name="measurementId" placeholder="Enter Measurement ID" value="<?php echo empty($otp_setting_data_2[0]->measurementId)?'':$otp_setting_data_2[0]->measurementId;  ?>" class="setting_input_field_1">                            
                <input type="hidden" name="Firebase" id="Firebase_decision" value="Yes">
            </div>                        
            <?php                     
                submit_button();                             
            ?>
        </div>
        <?php
        }
        public function twilio(){
            global $wpdb;
            
            $otp_setting_data_3 = $wpdb->get_results("SELECT * FROM " . WOO_OTP_DB_SETTING_TWILIO . " ORDER BY id DESC LIMIT 1");                    
            ?>
            <div class="twilio_Setting">
            <?php
                $instructions_obj= new WOO_OTP_INSTRUCT();    
                $instructions_obj->twilio_instruction();
            ?>
            <h3>Twilio OTP Setting</h3>
            <div>
                <label for="otp_length"><?php esc_html_e('Otp Length :','woo-otp-login-sms'); ?></label>                    
                <input type="text" id="otp_length_twilio" name="otp_length_twilio" placeholder="Enter the OTP Length" value="<?php echo empty($otp_setting_data_3[0]->otp_length)?'6':$otp_setting_data_3[0]->otp_length; ?>" class="setting_input_field">
            </div>
            <br>                    
            <div>
                <label class="custom_label"><?php esc_html_e('Otp Message Field :','woo-otp-login-sms'); ?></label>                    
                <textarea id="otp_message_field_twilio" name="otp_message_field_twilio" rows="4" cols="50"><?php  if(!empty($otp_setting_data_3[0]->otp_message)){ echo $otp_setting_data_3[0]->otp_message;}else{echo 'Your OTP is : {{otp}}';}?></textarea>                    
                <p><strong><?php esc_html_e('Note : ','woo-otp-login-sms'); ?></strong><?php esc_html_e('Place','woo-otp-login-sms'); ?><strong> {{otp}}</strong><?php esc_html_e(' for the otp in the message.','woo-otp-login-sms'); ?></p>
            </div>                                            
            <br>
            <div>
                <label for="otp_expire"><?php esc_html_e('Otp Expire Time :','woo-otp-login-sms'); ?></label>                   
                <input type="text" id="otp_expire_twilio" name="otp_expire_twilio" value="<?php echo empty($otp_setting_data_3[0]->otp_expire_time)?'10':$otp_setting_data_3[0]->otp_expire_time; ?>" class="setting_input_field" style="width:50px;"><span> Minutes</span>
            </div>             
            <br>
            <div>
                <label for="otp_resend_timer"><?php esc_html_e('Otp Resend Time :','woo-otp-login-sms'); ?></label>                   
                <input type="text" id="otp_resend_timer_twilio" name="otp_resend_timer_twilio" value="<?php echo empty($otp_setting_data_3[0]->otp_expire_time)?'10':$otp_setting_data_3[0]->otp_resend_timer_twilio; ?>"  class="setting_input_field" style="width:50px;"><span> Seconds</span>
            </div>
            <br>  
            <div>
                <label for="otp_length"><?php esc_html_e('Country Code :','woo-otp-login-sms'); ?></label>                    
                <?php 
                    if(!empty($otp_setting_data_3[0]->country_code)){
                ?>
                    <script>   
                        jQuery(document).ready(function(){
                            jQuery("select[name='countryCode_twilio']").find("option[selected]").removeAttr("selected");
                            jQuery("select[name='countryCode_twilio']").find("option[value='<?php  echo $otp_setting_data_3[0]->country_code ;?>']").attr("selected",'selected');
                        })                                    
                    </script>
                <?php  }  ?>
                <?php 
                        new COUNTRYCODE_FIELD('countryCode_twilio');
                ?>
            </div>                                                
            <br>                                                                                        
            <h3>Twilio Api Configuration</h3>                        
            <div>
                <label for="Api_key"><?php esc_html_e('Registered Number:','woo-otp-login-sms'); ?></label>                    
                <input type="text" id="twilio_register_no" name="twilio_register_no" placeholder="Enter Twilio Registered Number" value="<?php echo empty($otp_setting_data_3[0]->twilio_reg_no)?'':$otp_setting_data_3[0]->twilio_reg_no; ?>" class="setting_input_field_1">
            </div>
            <br>  
            <div>
                <label for="Api_key"><?php esc_html_e('Account SID :','woo-otp-login-sms'); ?></label>                    
                <input type="text" id="acc_sid" name="acc_sid_twilio" placeholder="Enter Account SID" value="<?php echo empty($otp_setting_data_3[0]->account_sid)?'':$otp_setting_data_3[0]->account_sid; ?>" class="setting_input_field_1">
            </div>
            <br>
            <div>
                <label for="Api_key"><?php esc_html_e('Auth Token :','woo-otp-login-sms'); ?></label>                    
                <input type="text" id="authToken" name="authToken" placeholder="Enter Auth Domain" value="<?php echo empty($otp_setting_data_3[0]->authToken)?'':$otp_setting_data_3[0]->authToken;  ?>" class="setting_input_field_1">                                                        
                <input type="hidden" name="Twilio" id="Twilio_decision" value="Yes">
            </div>                        
            <br>                        
            <?php                     
                submit_button();                             
            ?>
        </div>
        <?php
        }                    
    }
 }
