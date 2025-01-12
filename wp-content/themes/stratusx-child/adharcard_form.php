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

//& 2. Handle PHR Mobile Form Submission (PHR_mobile_auth_form_submit)
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
		wp_send_json_error(['message' => 'Error generating OTP. Please try again.', "response" => $response]);
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



//& Handle Mobile OTP Verification --- Completed
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

//& Get District and State 
function handle_get_states()
{
	$response = wp_remote_get('https://node-stage.healthray.com/api/v1/abha/m1-external/phr/state');

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => 'Failed to fetch states.']);
	} else {
		$body = json_decode(wp_remote_retrieve_body($response), true);
		if (!empty($body['status']) && $body['status'] === 200) {
			wp_send_json_success($body);
		} else {
			wp_send_json_error(['message' => 'No states found.', "body" =>  $body]);
		}
	}
}
add_action('wp_ajax_get_states', 'handle_get_states');
add_action('wp_ajax_nopriv_get_states', 'handle_get_states');

function handle_get_districts()
{
	$state_id = intval(sanitize_text_field($_GET['state_ID']));

	if (empty($state_id)) {
		wp_send_json_error(['message' => 'State ID is required.']);
	}

	$response = wp_remote_get("https://node-stage.healthray.com/api/v1/abha/m1-external/phr/district?state_id=$state_id");

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => 'Failed to fetch districts.']);
	} else {
		$body = json_decode(wp_remote_retrieve_body($response), true);
		if (!empty($body['status']) && $body['status'] === 200) {
			wp_send_json_success(['data' => $body['data'], "message" => $body['message']]);
		} else {
			wp_send_json_error(['message' => 'No districts found.', "state_id" => "https://node-stage.healthray.com/api/v1/abha/m1-external/phr/district?state_id=$state_id"]);
		}
	}
}
add_action('wp_ajax_get_districts', 'handle_get_districts');
add_action('wp_ajax_nopriv_get_districts', 'handle_get_districts');

//& phr/login --- Completed
// PHP Code
function handle_PHR_demographics_submit_ajax()
{
	// Retrieve and sanitize the posted data
	$transaction_id = sanitize_text_field($_POST['transaction_id']);
	$first_name = sanitize_text_field($_POST['first_name']);
	$middle_name = sanitize_text_field($_POST['middle_name']);
	$last_name = sanitize_text_field($_POST['last_name']);
	$pin_code = sanitize_text_field($_POST['pin_code']);
	$gender = sanitize_text_field($_POST['gender']);
	$dob = sanitize_text_field($_POST['dob']);
	$mobile_no = sanitize_text_field($_POST['mobile_no']);
	$state_code = sanitize_text_field($_POST['state_code']);
	$district_code = sanitize_text_field($_POST['district_code']);
	$address = sanitize_textarea_field($_POST['address'] ?? "Katargam, Surat");

	// Validate required fields
	if (empty($transaction_id) || empty($first_name) || empty($mobile_no)) {
		wp_send_json_error(['message' => 'Transaction ID, First Name, and Mobile Number are required.']);
	}

	// Prepare payload
	$payload = [
		'transaction_id' => $transaction_id,
		'first_name' => $first_name,
		'middle_name' => $middle_name,
		'last_name' => $last_name,
		'pin_code' => $pin_code,
		'gender' => $gender,
		'dob' => $dob,
		'mobile_no' => $mobile_no,
		'state_code' => $state_code,
		'district_code' => $district_code,
		'address' => $address,
	];

	// Call the API
	$response = wp_remote_post('https://node-stage.healthray.com/api/v1/abha/m1-external/phr/add_demographic_details', [
		'headers' => ['Content-Type' => 'application/json'],
		'body' => json_encode($payload),
	]);

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => 'Error submitting demographic details.', 'response' => $response]);
	} else {
		$body = json_decode(wp_remote_retrieve_body($response), true);

		if (!empty($body['status']) && $body['status'] === 200) {
			wp_send_json_success(['message' => $body['message'] ?? 'Demographics submitted successfully.']);
		} else {
			wp_send_json_error(['message' => $body['message'] ?? 'Unknown error occurred.','payload' => $payload]);
		}
	}

	wp_die();
}
add_action('wp_ajax_PHR_demographics_submit', 'handle_PHR_demographics_submit_ajax');
add_action('wp_ajax_nopriv_PHR_demographics_submit', 'handle_PHR_demographics_submit_ajax');



//& phr/login --- Completed
function handle_login_phr_address()
{
	$transaction_id = sanitize_text_field($_POST['transaction_id']);
	$phrAddress = sanitize_text_field($_POST['phr_address']);

	if (empty($transaction_id) || empty($phrAddress)) {
		wp_send_json_error(["post" => $_POST, 'message' => 'Transaction ID and phrAddress are required.']);
	}

	$payloadBody = [
		"transaction_id" => $transaction_id,
		"phr_address" => $phrAddress,
		"is_health_number" => false,
		"already_registered" => true
	];
	$response = wp_remote_post('https://node-stage.healthray.com/api/v1/abha/m1-external/phr/login', [
		'headers' => ['Content-Type' => 'application/json'],
		'body' => json_encode($payloadBody),
	]);

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => 'Failed to Logi using PHR address.']);
	} else {
		$headers = wp_remote_retrieve_headers($response);
		$body = json_decode(wp_remote_retrieve_body($response), true);

		$content_type = isset($headers['content-type']) ? $headers['content-type'] : '';

		if (strpos($content_type, 'image/') !== false) {
			$image_url = 'data:' . $content_type . ';base64,' . base64_encode($body);
			wp_send_json_success([
				'message' => 'Image received.',
				'image_url' => $image_url,
				'downloadable' => true
			]);
		} else {
			wp_send_json_error(['message' => $body['status'] . ' : ' . $body['message'] ?? 'Unknown error occurred.']);
		}
	}

	wp_send_json_error(["post" => $_POST]);
}

add_action('wp_ajax_login_phr_address', 'handle_login_phr_address');
add_action('wp_ajax_nopriv_login_phr_address', 'handle_login_phr_address');
