<?php
// Aadhaar Authentication Shortcode
function adhar_auth_form_shortcode()
{
	// Start output buffering
	ob_start();
	require_once(__DIR__ . '/lib/abhacard_form.php');
	return ob_get_clean();
}
add_shortcode('adharAuthForm', 'adhar_auth_form_shortcode');






//& 1. aadhaar/generate_otp Handle Aadhaar Form Submission

//! -- 1.1 - m1-external/aadhaar/generate_otp -----------
function handle_aadhaar_form_submit_ajax()
{
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

//! -- 1.2 - m1-external/aadhaar/verify_otp -----------
// Verify OTP via AJAX
function verify_aadhaar_otp_ajax()
{
	$otp = sanitize_text_field($_POST['otp']);
	$transactionId = sanitize_text_field($_POST['transactionID']);
	$otpMobileno = sanitize_text_field($_POST['number']);

	if (empty($otp) || empty($transactionId)) {
		wp_send_json_error(["post" => $_POST, 'message' => 'OTP and Transaction ID are required.']);
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

		$content_type = isset($headers['content-type']) ? $headers['content-type'] : '';

		if (strpos($content_type, 'image/') !== false) {
			$image_url = 'data:' . $content_type . ';base64,' . base64_encode($body);
			wp_send_json_success([
				'message' => 'Image received.',
				'image_url' => $image_url,
				'downloadable' => true
			]);
		} else {
			// Assume JSON response
			$data = json_decode($body, true);

			if (json_last_error() !== JSON_ERROR_NONE) {
				wp_send_json_error(['message' => 'Invalid JSON response from API.']);
			}

			if (!empty($data['status']) && $data['status'] === 200) {
				if (!empty($data['data']['otp_required']) && $data['data']['otp_required'] === true) {
					wp_send_json_success([
						'message' => $data['message'] ?? 'OTP required again.',
						'otp_required' => true,
						'data' => $data['data'],
					]);
				} else {
					wp_send_json_success([
						'message' => $data['message'] ?? 'OTP verified successfully.',
						'transactionId' => $data['data']['transaction_id'],
						'data' => $data['data'],
					]);
				}
			} else {
				wp_send_json_error(['message' => $data['message'] ?? 'Verification failed.']);
			}
		}
	}

	wp_die();
}
add_action('wp_ajax_verify_aadhaar_otp', 'verify_aadhaar_otp_ajax');
add_action('wp_ajax_nopriv_verify_aadhaar_otp', 'verify_aadhaar_otp_ajax');

// -----------------------------------------------------------------------------------------------------

//& 2. Handle PHR Mobile Form Submission
function handle_PHR_mobile_form_submit_ajax()
{
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
				'transactionId' => $body['data']['transactionId'],
			]);
		} else {
			wp_send_json_error(['message' => $body['data'][0] ?? 'Unknown error occurred.']);
		}
	}

	wp_die();
}
add_action('wp_ajax_PHR_mobile_auth_form_submit', 'handle_PHR_mobile_form_submit_ajax');
add_action('wp_ajax_nopriv_PHR_mobile_auth_form_submit', 'handle_PHR_mobile_form_submit_ajax');



// Handle Mobile OTP Verification
function handle_mobile_PHR_otp()
{
	$transaction_id = sanitize_text_field($_POST['transactionID']);
	$otp = sanitize_text_field($_POST['otp']);

	if (empty($transaction_id) || empty($otp)) {
		wp_send_json_error(["post" => $_POST, 'message' => 'Transaction ID and OTP are required.']);
	}

	$response = wp_remote_post('https://node-stage.healthray.com/api/v1/abha/m1-external/phr/verify_otp', [
		'headers' => ['Content-Type' => 'application/json'],
		'body' =>
		json_encode(['transaction_id' => $transaction_id, 'otp' => $otp,  "is_health_number" => false,]),
	]);

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => 'Failed to verify OTP.']);
	}

	$response_body = json_decode(wp_remote_retrieve_body($response), true);

	if (!empty($response_body['status']) && $response_body['status'] === 200) {
		wp_send_json_success([
			'message' => $response_body['message'],
			"transactionId" => $response_body['data']['transactionId'],
			"mappedPhrAddress" => $response_body['data']['mappedPhrAddress'],
			"data" => $response_body
		]);
	} else {
		wp_send_json_error([
			'message' => $response_body['message'] ?? 'Verification failed.',
			"post" => $_POST,
			"data" => $response_body
		]);
	}

	wp_die();
}
add_action('wp_ajax_verify_PHR_otp', 'handle_mobile_PHR_otp');
add_action('wp_ajax_nopriv_verify_PHR_otp', 'handle_mobile_PHR_otp');
