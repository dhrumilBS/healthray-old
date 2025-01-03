<?php
function adhar_auth_form_shortcode()
{
    // Start output buffering
    ob_start();
?>
    <div class="section-wrapper">
        <div class="content-sec">
            <h2> Create Ayushman Bharat Health Account ABHA (Health ID) Card </h2>
        </div>

        <div class="link-action">
            <button class="link-action-btn active" data-view="aadhaar">Aadhaar Number</button>
            <button class="link-action-btn" data-view="mobile">Mobile Number</button>
        </div>

        <div class="form-container">
            <form id="common-form">
                <input type="hidden" name="action" value="adhar_auth_form_submit">

                <div class="form-field aadhaar-field active">
                    <input type="text" id="aadhaar-input" name="aadhaar" class="input-field" maxlength="12" placeholder="XXXX XXXX XXXX" required>
                </div>

                <div class="form-field mobile-field">
                    <input type="tel" id="mobile-input" name="mobile" class="input-field" pattern="[0-9]{10}" placeholder="Enter your mobile number" required disabled>
                </div>

                <div class="checkbox-input">
                    <input type="checkbox" id="permissions" required>
                    <label for="permissions">I agree to the necessary permissions for Healthray to set up my ABHA Health Locker. <a href="#">Learn More</a></label>
                </div>

                <div class="form-submit">
                    <button type="submit" id="submit-button" class="submit-btn">Submit Aadhaar</button>
                </div>
            </form>

            <div id="otp-section" style="display:none;">
                <form id="otp-form">
                    <input type="text" id="otp-input" class="input-field" placeholder="Enter OTP" maxlength="6" required>
                    <button type="submit" class="submit-btn">Verify OTP</button>
                </form>
                <button id="resend-otp" class="submit-btn" disabled>Resend OTP</button>
            </div>

            <div id="loading-spinner" style="display:none;">Loading...</div>
            <div id="response-message"></div>
        </div>
    </div>

<?php
    // Return the buffered content
    return ob_get_clean();
}
add_shortcode('adharAuthForm', 'adhar_auth_form_shortcode');

// Handle form submission via AJAX
function adhar_auth_form_submit_ajax()
{
    if (isset($_POST['aadhaar']) && !empty($_POST['aadhaar'])) {
        $aadhaar_number = sanitize_text_field($_POST['aadhaar']);

        if (strlen($aadhaar_number) === 12) {
            $response = wp_remote_post('https://node-stage.healthray.com/api/v2/abha/m1-external/aadhaar/generate_otp', [
                'headers' => ['Content-Type' => 'application/json'],
                'body'    => json_encode(['aadhaar' => $aadhaar_number]),
            ]);


            if (is_wp_error($response)) {
                wp_send_json_error(['message' => 'Error generating OTP. Please try again.',$response]);
            } else {
                $body = json_decode(wp_remote_retrieve_body($response), true);
                if ($body['success']) {
                    wp_send_json_success(['message' => 'OTP sent successfully.', 'transactionId' => $body['transactionId'], $response]);
                } else {
                    wp_send_json_error(['message' => $body['message']]);
                }
            }
        } else {
            wp_send_json_error(['message' => 'Invalid Aadhaar number.']);
        }
    } else {
        wp_send_json_error(['message' => 'Please enter your Aadhaar number.']);
    }
    wp_die();
}

// Verify OTP via AJAX
function verify_otp_ajax()
{
    if (isset($_POST['otp']) && isset($_POST['transactionId'])) {
        $otp = sanitize_text_field($_POST['otp']);
        $transactionId = sanitize_text_field($_POST['transactionId']);

        $response = wp_remote_post('https://node-stage.healthray.com/api/v1/abha/m1-external/aadhaar/verify_otp', [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode(['otp' => $otp, 'transactionId' => $transactionId]),
        ]);

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => 'Error verifying OTP. Please try again.']);
        } else {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            if ($body['success']) {
                wp_send_json_success(['message' => 'OTP verified successfully.']);
            } else {
                wp_send_json_error(['message' => $body['message']]);
            }
        }
    } else {
        wp_send_json_error(['message' => 'Invalid OTP or transaction ID.']);
    }
    wp_die();
}
add_action('wp_ajax_adhar_auth_form_submit', 'adhar_auth_form_submit_ajax');
add_action('wp_ajax_nopriv_adhar_auth_form_submit', 'adhar_auth_form_submit_ajax');
add_action('wp_ajax_verify_otp', 'verify_otp_ajax');
add_action('wp_ajax_nopriv_verify_otp', 'verify_otp_ajax');
