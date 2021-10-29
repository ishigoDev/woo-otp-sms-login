<?php
if(!class_exists('WOO_OTP_INSTRUCT')){
    class WOO_OTP_INSTRUCT{
        public function firebase_instruction(){
            ?>
            <div>
                <h3 style="margin-bottom:0px;"><?php esc_html_e('Firbase Instructions','woo-otp-login-sms'); ?></h3>
                <p style="color:red;"><strong><?php esc_html_e('Firebase can be used for all countries phone number code.','woo-otp-login-sms'); ?></strong></p>                
                <p><?php esc_html_e('Please get the api configuration from firebase console first and then fill it below .','woo-otp-login-sms'); ?> </p>                
                <p><strong><?php esc_html_e('Note: Please authorized your SITE DOMAIN on the firebase before use.','woo-otp-login-sms'); ?></strong></p>                
            </div>            
            <?php
        }
        public function fast2sms_instruction(){
            ?>
            <div>
                <h3 style="margin-bottom:0px;"><?php esc_html_e('Fast2Sms Instructions','woo-otp-login-sms'); ?></h3>
                <p style="color:red;"><strong><?php esc_html_e('Fast2Sms can be used for India phone number code only.','woo-otp-login-sms'); ?></strong></p>
                <p><?php esc_html_e(' Get the Api configuration from','woo-otp-login-sms');?> <a href="https://www.fast2sms.com/"><?php esc_html_e('Fast2Sms','woo-otp-login-sms'); ?></a>.</p>                     
            </div>
            <?php
        }
        public function twilio_instruction(){
            ?>
            <div>
                <h3 style="margin-bottom:0px;"><?php esc_html_e('Twilio Instructions','woo-otp-login-sms'); ?></h3>
                <p style="color:red;"><strong><?php esc_html_e('Twilio can be used for all countries phone number code.','woo-otp-login-sms'); ?></strong></p>
                <p><?php esc_html_e(' Get the Api configuration from','woo-otp-login-sms');?> <a href="https://www.twilio.com/"><?php esc_html_e('Twilio','woo-otp-login-sms'); ?></a>.</p>                     
            </div>
            <?php
        }
    }
}