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


        <div class="form-container">
            <div id="common-section">
                <div class="link-action">
                    <button class="link-action-btn active" data-view="aadhaar">Aadhaar Number</button>
                    <button class="link-action-btn" data-view="mobile">Mobile Number</button>
                </div>

                <form id="common-form">
                    <input type="hidden" name="action" value="adhar_auth_form_submit">

                    <div class="form-field aadhaar-field active">
                        <input type="text" id="aadhaar-input" name="aadhaar" class="input-field" maxlength="12" placeholder="XXXXXXXXXXXX" required>
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
            </div>

            <div id="otp-section">
                <form id="otp-form">
                    <input type="hidden" name="action" value="verify_otp">

                    <div class="form-field d-flex ">
                        <input type="text" id="otp-input" class="input-field" placeholder="Enter OTP" maxlength="6" required>
                        <div class="form-submit resend-btn">
                            <a id="resend-otp" class="submit-link" href="javascript:void(0)" disabled>Resend OTP</a>
                            <a id="change-mobile" class="submit-link" href="javascript:void(0)" disabled>Change Mobile</a>
                        </div>
                    </div>

                    <div class="second-mobile-number">
                        <div class="form-field">
                            <input type="tel" id="otp-mobile-input" name="otp-mobile" class="input-field" pattern="[0-9]{10}" placeholder="Enter your mobile number" required>
                        </div>
                        <div class="d-flex info-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 512 512">
                                <path fill="currentColor" d="M256 40c118.621 0 216 96.075 216 216 0 119.291-96.61 216-216 216-119.244 0-216-96.562-216-216 0-119.203 96.602-216 216-216m0-32C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm-36 344h12V232h-12c-6.627 0-12-5.373-12-12v-8c0-6.627 5.373-12 12-12h48c6.627 0 12 5.373 12 12v140h12c6.627 0 12 5.373 12 12v8c0 6.627-5.373 12-12 12h-72c-6.627 0-12-5.373-12-12v-8c0-6.627 5.373-12 12-12zm36-240c-17.673 0-32 14.327-32 32s14.327 32 32 32 32-14.327 32-32-14.327-32-32-32z"></path>
                            </svg>
                            <div class="">This mobile number will be used for all the communications</div>
                        </div>
                    </div>

                    <div class="form-submit">
                        <button type="submit" class="submit-btn">Verify OTP</button>
                    </div>
                </form>
            </div>

            <div id="loading-spinner" style="display: none;">
                <span>Loading...</span>
            </div>
        </div>
        <div id="response-message"></div>
    </div>

<?php
    // Return the buffered content
    return ob_get_clean();
}
add_shortcode('adharAuthForm', 'adhar_auth_form_shortcode');

// Handle form submission via AJAX
function adhar_auth_form_submit_ajax()
{
    $aadhaar_number = sanitize_text_field($_POST['aadhaar']);

    $response = wp_remote_post('https://node-stage.healthray.com/api/v2/abha/m1-external/aadhaar/generate_otp', [
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => json_encode(['aadhaar' => $aadhaar_number]),
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Error generating OTP. Please try again.']);
    } else {
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (!empty($body['success']) && $body['success'] === true) {
            wp_send_json_success([
                'message'       => 'OTP sent successfully.',
                'transaction_id' => $body['transaction_id']
            ]);
        } else {
            $errorMessage = isset($body) ? $body : 'Unknown error occurred.';
            wp_send_json_error(['message' => $errorMessage]);
        }
    }

    wp_die();
}
add_action('wp_ajax_adhar_auth_form_submit', 'adhar_auth_form_submit_ajax');
add_action('wp_ajax_nopriv_adhar_auth_form_submit', 'adhar_auth_form_submit_ajax');

// Verify OTP via AJAX
function verify_otp_ajax()
{
        if (isset($_POST['otp']) && isset($_POST['transaction_id']) && isset($_POST['otpMobileno'])) {
            $otp = sanitize_text_field($_POST['otp']);
            $transaction_id = sanitize_text_field($_POST['transaction_id']);
            $otpMobileno = sanitize_text_field($_POST['otpMobileno']);

            $response = wp_remote_post('https://node-stage.healthray.com/api/v2/abha/m1-external/aadhaar/verify_otp', [
                'headers' => ['Content-Type' => 'application/json'],
                'body'    => json_encode(['otp' => $otp, 'transaction_id' => $transaction_id, "number" => $otpMobileno]),
            ]);

            if (!(is_wp_error($response))) {
                $body = json_decode(wp_remote_retrieve_body($response), true);
                if ($body['success']) {
                    wp_send_json_success(['message' => 'OTP verified successfully.']);
                } else {
                    wp_send_json_error(['message' => $body['message']]);
                }
            } else {
                wp_send_json_error(['message' => 'Error verifying OTP. Please try again.']);
            }
        } else {
            wp_send_json_error(['message' => 'Invalid OTP or transaction ID.']);
        }
        wp_die();
}
add_action('wp_ajax_verify_otp', 'verify_otp_ajax');
add_action('wp_ajax_nopriv_verify_otp', 'verify_otp_ajax');
