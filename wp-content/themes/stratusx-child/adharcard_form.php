<?php
// Aadhaar Authentication Shortcode
function adhar_auth_form_shortcode() {
    // Start output buffering
    ob_start();
    require_once(__DIR__ . '/lib/abhacard_form.php');
    return ob_get_clean();
}
add_shortcode('adharAuthForm', 'adhar_auth_form_shortcode');

// Handle Aadhaar Form Submission
function handle_aadhaar_form_submit_ajax() {
    $aadhaar_number = sanitize_text_field($_POST['value']);

    if (empty($aadhaar_number)) {
        wp_send_json_error(['message' => 'Aadhaar number is required.']);
    }

    $response = wp_remote_post('https://node-stage.healthray.com/api/v2/abha/m1-external/aadhaar/generate_otp', [
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => json_encode(['aadhaar' => $aadhaar_number]),
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Error generating OTP. Please try again.']);
    } else {
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (!empty($body['status']) && $body['status'] === 200) {
            wp_send_json_success([
                'message'       => $body['message'] ?? 'OTP sent successfully.',
                'data'          => $body['data'],
                'transactionId' => $body['data']['transaction_id'],
            ]);
        } else {
            wp_send_json_error(['message' => $body['message'] ?? 'Unknown error occurred.']);
        }
    }

    wp_die();
}
add_action('wp_ajax_aadhaar_auth_form_submit', 'handle_aadhaar_form_submit_ajax');
add_action('wp_ajax_nopriv_aadhaar_auth_form_submit', 'handle_aadhaar_form_submit_ajax');

// Verify OTP via AJAX
function verify_otp_ajax() {
    $otp = sanitize_text_field($_POST['otp']);
    $transactionId = sanitize_text_field($_POST['transaction_id']);
    $otpMobileno = sanitize_text_field($_POST['number']);

    if (empty($otp) || empty($transactionId)) {
        wp_send_json_error(['message' => 'OTP and Transaction ID are required.']);
    }

    $response = wp_remote_post('https://node-stage.healthray.com/api/v2/abha/m1-external/aadhaar/verify_otp', [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode([
            'otp'           => $otp,
            'transaction_id' => $transactionId,
            'number'        => $otpMobileno,
        ]),
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Error verifying OTP. Please try again.']);
    } else {

        $headers = wp_remote_retrieve_headers($response);
        $body = wp_remote_retrieve_body($response);

        if (!empty($body['status']) && $body['status'] === 200) {
            wp_send_json_success([
                'message'       => $body['message'] ?? 'OTP verified successfully.',
                'transactionId' => $body['data']['transaction_id'],
                'data'          => $body['data'],
            ]);
        } else {
            wp_send_json_error(['message' => $body ?? 'Verification failed.']);
        }
    }

    wp_die();
}
add_action('wp_ajax_verify_otp', 'verify_otp_ajax');
add_action('wp_ajax_nopriv_verify_otp', 'verify_otp_ajax');

// Handle Mobile OTP Verification
function handle_mobile_verify_otp() {
    $transaction_id = sanitize_text_field($_POST['transaction_id']);
    $otp = sanitize_text_field($_POST['otp']);

    if (empty($transaction_id) || empty($otp)) {
        wp_send_json_error(['message' => 'Transaction ID and OTP are required.']);
    }

    $response = wp_remote_post('https://node-stage.healthray.com/api/v2/abha/m1-external/mobile/verify_otp', [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode(['transaction_id' => $transaction_id, 'otp' => $otp]),
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Failed to verify OTP.']);
    }

    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    if (!empty($response_body['status']) && $response_body['status'] === 200) {
        wp_send_json_success(['message' => $response_body['data']['message'] ?? 'OTP verified successfully.']);
    } else {
        wp_send_json_error(['message' => $response_body['message'] ?? 'Verification failed.']);
    }

    wp_die();
}
add_action('wp_ajax_verify_mobile_otp', 'handle_mobile_verify_otp');
add_action('wp_ajax_nopriv_verify_mobile_otp', 'handle_mobile_verify_otp');

// Handle Mobile Form Submission
function handle_mobile_form_submit_ajax() {
    $mobile_number = sanitize_text_field($_POST['number']);

    if (empty($mobile_number)) {
        wp_send_json_error(['message' => 'Mobile number is required.']);
    }

    $response = wp_remote_post('https://node-stage.healthray.com/api/v1/abha/m1-external/phr/generate_otp', [
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => json_encode(['number' => $mobile_number, 'is_health_number' => false]),
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Error generating OTP. Please try again.']);
    } else {
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (!empty($body['status']) && $body['status'] === 200) {
            wp_send_json_success([
                'message'       => $body['message'] ?? 'OTP sent successfully.',
                'transactionId' => $body['data']['transaction_id'],
            ]);
        } else {
            wp_send_json_error(['message' => $body['message'] ?? 'Unknown error occurred.']);
        }
    }

    wp_die();
}
add_action('wp_ajax_mobile_auth_form_submit', 'handle_mobile_form_submit_ajax');
add_action('wp_ajax_nopriv_mobile_auth_form_submit', 'handle_mobile_form_submit_ajax');
