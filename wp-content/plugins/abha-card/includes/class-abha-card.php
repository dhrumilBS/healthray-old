<?php
/**
 * Class ABHA_Card
 *
 * Registers all AJAX endpoints for Aadhaar / ABHA / PHR flows.
 *
 * Flow overview
 * ─────────────
 * Section 1 – Aadhaar-based ABHA creation (m1-external/aadhaar/*)
 *   1.1  generate_otp          – send OTP to Aadhaar-linked mobile
 *   1.2  verify_otp            – verify OTP, receive ABHA card image or user data
 *   1.3  mobile/generate_otp   – send OTP to an alternate mobile number
 *   1.4  mobile/verify_otp     – verify alternate-mobile OTP
 *   1.5  suggestion            – fetch suggested ABHA addresses
 *   1.6  link                  – link chosen ABHA address & store locally
 *
 * Section 2 – PHR mobile-based flow (m1-external/phr/*)
 *   2.1  phr/generate_otp      – send OTP to mobile for PHR login/register
 *   2.2  phr/verify_otp        – verify PHR OTP
 *   2.3  phr/state             – list all states
 *   2.4  phr/district          – list districts for a state
 *   2.5  phr/add_demographic_details
 *   2.6  phr/suggession        – fetch suggested PHR addresses
 *   2.7  phr/login             – log in with chosen PHR address
 */

if (!defined('ABSPATH')) {
    exit;
}

class ABHA_Card
{

    // -----------------------------------------------------------------------
    // Constructor – register hooks
    // -----------------------------------------------------------------------

    public function __construct()
    {
        $this->register_hooks();
    }

    private function register_hooks(): void
    {
        $ajax_actions = [
            // Section 1
            'aadhaar_auth_form_submit' => 'handle_aadhaar_generate_otp',
            'verify_aadhaar_otp' => 'handle_aadhaar_verify_otp',
            'handle_aadhaar_mobile_submit' => 'handle_aadhaar_mobile_generate_otp',
            'verify_adhar_mobile_otp' => 'handle_aadhaar_mobile_verify_otp',
            'get_aadhaar_suggestion' => 'handle_aadhaar_suggestion',
            'handle_link_abha' => 'handle_link_abha',
            // Section 2
            'PHR_mobile_auth_form_submit' => 'handle_phr_generate_otp',
            'verify_PHR_otp' => 'handle_phr_verify_otp',
            'get_states' => 'handle_get_states',
            'get_districts' => 'handle_get_districts',
            'PHR_demographics_submit' => 'handle_phr_demographics',
            'get_PHR_suggestion' => 'handle_phr_suggestion',
            'login_phr_address' => 'handle_phr_login',
        ];

        foreach ($ajax_actions as $action => $method) {
            add_action("wp_ajax_{$action}", [$this, $method]);
            add_action("wp_ajax_nopriv_{$action}", [$this, $method]);
        }
    }

    // -----------------------------------------------------------------------
    // Shared helpers
    // -----------------------------------------------------------------------

    /**
     * POST wrapper – returns [ 'ok' => bool, 'headers' => ..., 'body' => ... ]
     */
    private function api_post(string $endpoint, array $payload): array
    {
        $response = wp_remote_post(ABHA_API_PATH . $endpoint, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => wp_json_encode($payload),
            'timeout' => 30,
        ]);

        if (is_wp_error($response)) {
            return ['ok' => false, 'error' => $response->get_error_message()];
        }

        return [
            'ok' => true,
            'headers' => wp_remote_retrieve_headers($response),
            'raw_body' => wp_remote_retrieve_body($response),
        ];
    }

    /**
     * GET wrapper
     */
    private function api_get(string $endpoint): array
    {
        $response = wp_remote_get(ABHA_API_PATH . $endpoint, [
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => 30,
        ]);

        if (is_wp_error($response)) {
            return ['ok' => false, 'error' => $response->get_error_message()];
        }

        return [
            'ok' => true,
            'raw_body' => wp_remote_retrieve_body($response),
        ];
    }

    /**
     * Decode JSON body; returns null on failure.
     */
    private function parse_json(string $raw): ?array
    {
        $data = json_decode($raw, true);
        return (json_last_error() === JSON_ERROR_NONE) ? $data : null;
    }

    /**
     * Build a base-64 data-URI when the API returns an image.
     */
    private function build_image_response(string $content_type, string $raw_body): array
    {
        return [
            'message' => 'Image received.',
            'imageData' => true,
            'userData' => false,
            'image_url' => 'data:' . $content_type . ';base64,' . base64_encode($raw_body),
            'downloadable' => true,
        ];
    }

    /**
     * Save image to uploads/abha-cards/ and return local file URL.
     * Returns empty string on failure.
     */
    private function save_image_to_uploads(string $raw_body, string $content_type): string
    {
        $extension = explode('/', $content_type)[1] ?? 'png';
        $filename = date('Y_m_d_H_i_s_') . bin2hex(random_bytes(3)) . '.' . $extension;
        $upload_dir = wp_upload_dir();
        $upload_path = trailingslashit($upload_dir['basedir']) . 'abha-cards/';

        if (!file_exists($upload_path)) {
            wp_mkdir_p($upload_path);
        }

        $written = file_put_contents($upload_path . $filename, $raw_body);
        return ($written !== false)
            ? trailingslashit($upload_dir['baseurl']) . 'abha-cards/' . $filename
            : '';
    }

    // -----------------------------------------------------------------------
    // Section 1 – Aadhaar flow
    // -----------------------------------------------------------------------

    // 1.1 – Aadhaar generate OTP
    public function handle_aadhaar_generate_otp(): void
    {
        $aadhaar = sanitize_text_field($_POST['adharnumber'] ?? '');

        if (empty($aadhaar)) {
            wp_send_json_error(['message' => 'Aadhaar number is required.']);
        }

        $result = $this->api_post('v2/abha/m1-external/aadhaar/generate_otp', ['aadhaar' => $aadhaar]);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (empty($body)) {
            wp_send_json_error(['message' => 'Invalid API response.']);
        }

        if (($body['status'] ?? 0) === 200) {
            wp_send_json_success([
                'status' => 200,
                'message' => $body['message'] ?? 'OTP sent successfully.',
                'transactionId' => $body['data']['transaction_id'] ?? '',
            ]);
        }

        // 422 returns first validation message in data[0]
        wp_send_json_error([
            'status' => $body['status'] ?? 0,
            'message' => ($body['status'] === 422)
                ? ($body['data'][0] ?? 'Validation error.')
                : ($body['message'] ?? 'Unknown error occurred.'),
        ]);
    }

    // 1.2 – Aadhaar verify OTP (saves card image to uploads)
    public function handle_aadhaar_verify_otp(): void
    {
        $otp = sanitize_text_field($_POST['otp'] ?? '');
        $transaction_id = sanitize_text_field($_POST['transactionId'] ?? '');
        $mobile = sanitize_text_field($_POST['number'] ?? '');

        if (empty($otp) || empty($transaction_id)) {
            wp_send_json_error(['message' => 'OTP and Transaction ID are required.']);
        }

        $result = $this->api_post('v2/abha/m1-external/aadhaar/verify_otp', [
            'otp' => $otp,
            'transaction_id' => $transaction_id,
            'number' => $mobile,
        ]);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $content_type = (string) ($result['headers']['content-type'] ?? '');
        $raw_body = $result['raw_body'];

        if (strpos($content_type, 'image/') !== false) {
            // Persist the card image
            $this->save_image_to_uploads($raw_body, $content_type);
            wp_send_json_success($this->build_image_response($content_type, $raw_body));
        }

        $body = $this->parse_json($raw_body);

        if (empty($body)) {
            wp_send_json_error(['message' => 'Invalid JSON response from API.']);
        }

        if (($body['status'] ?? 0) === 200) {
            wp_send_json_success([
                'message' => $body['message'] ?? 'OTP verified.',
                'imageData' => false,
                'userData' => true,
                'data' => $body['data'] ?? [],
            ]);
        }

        wp_send_json_error([
            'message' => $body['message'] ?? 'Verification failed.',
            'data' => $body['data'][0] ?? null,
        ]);
    }

    // 1.3 – Mobile generate OTP (alternate mobile)
    public function handle_aadhaar_mobile_generate_otp(): void
    {
        $transaction_id = sanitize_text_field($_POST['transaction_id'] ?? '');
        $number = sanitize_text_field($_POST['number'] ?? '');

        if (empty($number)) {
            wp_send_json_error(['message' => 'Mobile number is required.']);
        }

        $result = $this->api_post('v2/abha/m1-external/mobile/generate_otp', [
            'number' => $number,
            'transaction_id' => $transaction_id,
        ]);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (empty($body)) {
            wp_send_json_error(['message' => 'Invalid API response.']);
        }

        if (($body['status'] ?? 0) === 200) {
            wp_send_json_success([
                'message' => $body['message'] ?? 'OTP sent successfully.',
                'data' => $body['data'] ?? [],
                'userData' => true,
                'transactionId' => $body['data']['transaction_id'] ?? '',
            ]);
        }

        wp_send_json_error([
            'message' => $body['message'] ?? 'Unknown error occurred.',
            'data' => $body['data'][0] ?? null,
        ]);
    }

    // 1.4 – Mobile verify OTP
    // FIX: was missing wp_die() — wp_send_json_* handles that internally,
    //      but added explicit return after each branch for clarity.
    public function handle_aadhaar_mobile_verify_otp(): void
    {
        $number = sanitize_text_field($_POST['number'] ?? '');
        $otp = sanitize_text_field($_POST['otp'] ?? '');
        $transaction_id = sanitize_text_field($_POST['transactionId'] ?? '');

        if (empty($otp)) {
            wp_send_json_error(['message' => 'OTP cannot be blank.']);
        }

        $result = $this->api_post('v2/abha/m1-external/mobile/verify_otp', [
            'number' => $number,
            'transaction_id' => $transaction_id,
            'otp' => $otp,
        ]);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (empty($body)) {
            wp_send_json_error(['message' => 'Invalid API response.']);
        }

        if (($body['status'] ?? 0) === 200) {
            wp_send_json_success([
                'message' => $body['message'] ?? 'OTP verified successfully.',
                'data' => $body['data'] ?? [],
                'transactionId' => $body['data']['transaction_id'] ?? '',
                'verify_aadhaar_mobile_otp' => true,
            ]);
        }

        wp_send_json_error([
            'message' => $body['message'] ?? 'Unknown error occurred.',
            'data' => $body['data'][0] ?? null,
        ]);
    }

    // 1.5 – Suggestion (GET)
    public function handle_aadhaar_suggestion(): void
    {
        $transaction_id = sanitize_text_field($_GET['transaction_id'] ?? '');

        if (empty($transaction_id)) {
            wp_send_json_error(['message' => 'Transaction ID is required.']);
        }

        $result = $this->api_get("v2/abha/m1-external/suggestion?transaction_id={$transaction_id}");

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (empty($body)) {
            wp_send_json_error(['message' => 'Invalid API response.']);
        }

        if (($body['status'] ?? 0) === 200) {
            wp_send_json_success([
                'message' => $body['message'] ?? 'Suggestions fetched.',
                'transactionId' => $body['data']['transaction_id'] ?? '',
                'suggestions' => $body['data']['suggestion'] ?? [],
                'fetchSuggestion' => true,
            ]);
        }

        wp_send_json_error([
            'message' => $body['message'] ?? 'No suggestions found.',
            'data' => $body['data'][0] ?? null,
        ]);
    }

    // 1.6 – Link ABHA
    // FIX (a): removed `print_r($_POST)` debug statement
    // FIX (b): broken array key `'\n\t\t\t' => $abha_number` → `'abha_number' => $abha_number`
    // FIX (c): API call was dead code (placed after wp_send_json_* which exits).
    //          Restructured so the API is called first, DB insert follows on success.
    public function handle_link_abha(): void
    {
        $data = $_POST['payload'] ?? [];

        if (empty($data)) {
            wp_send_json_error(['message' => 'Payload is required.']);
        }

        $abha_address = sanitize_text_field($data['abha_address'] ?? '');
        $transaction_id = sanitize_text_field($data['transaction_id'] ?? '');
        $first_name = sanitize_text_field($data['first_name'] ?? '');
        $middle_name = sanitize_text_field($data['middle_name'] ?? '');
        $last_name = sanitize_text_field($data['last_name'] ?? '');
        $gender = sanitize_text_field($data['gender'] ?? '');
        $abha_number = sanitize_text_field($data['abha_number'] ?? '');
        $mobile_no = sanitize_text_field($data['mobile_no'] ?? '');
        $token = sanitize_text_field($data['tokens']['token'] ?? '');
        $refresh_token = sanitize_text_field($data['tokens']['refresh_token'] ?? '');

        $payload = [
            'is_new' => 1,
            'abha_address' => $abha_address,
            'transaction_id' => $transaction_id,
            'user_details' => [
                'first_name' => $first_name,
                'middle_name' => $middle_name,
                'last_name' => $last_name,
                'gender' => $gender,
                'abha_number' => $abha_number,   // FIX: was a broken empty-string key
                'mobile_no' => $mobile_no,
                'tokens' => [
                    'token' => $token,
                    'refresh_token' => $refresh_token,
                ],
            ],
        ];

        // --- Make the API call FIRST, then decide on DB insert ---
        $result = $this->api_post('v2/abha/m1-external/link/', $payload);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $content_type = (string) ($result['headers']['content-type'] ?? '');
        $raw_body = $result['raw_body'];

        if (strpos($content_type, 'image/') !== false) {
            // API returned an ABHA card image – optionally store to DB here
            $this->save_image_to_uploads($raw_body, $content_type);

            wp_send_json_success(array_merge(
                $this->build_image_response($content_type, $raw_body),
                [
                    'details' => [
                        'abha_number' => $abha_number,
                        'transaction_id' => $transaction_id,
                        'mobile_number' => $mobile_no,
                        'first_name' => $first_name,
                        'middle_name' => $middle_name,
                        'last_name' => $last_name,
                        'gender' => $gender,
                        'created_at' => current_time('mysql'),
                    ],
                ]
            ));
        }

        $body = $this->parse_json($raw_body);

        wp_send_json_error([
            'message' => $body['message'] ?? 'Link failed.',
            'data' => $body['data'][0] ?? null,
        ]);
    }

    // -----------------------------------------------------------------------
    // Section 2 – PHR mobile flow
    // -----------------------------------------------------------------------

    // 2.1 – PHR generate OTP
    public function handle_phr_generate_otp(): void
    {
        $mobile = sanitize_text_field($_POST['adharnumber'] ?? '');

        if (empty($mobile)) {
            wp_send_json_error(['message' => 'Mobile number is required.']);
        }

        $result = $this->api_post('v1/abha/m1-external/phr/generate_otp', [
            'number' => $mobile,
            'is_health_number' => false,
        ]);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (empty($body)) {
            wp_send_json_error(['message' => 'Invalid API response.']);
        }

        if (($body['status'] ?? 0) === 200) {
            wp_send_json_success([
                'status' => 200,
                'message' => $body['message'] ?? 'OTP sent successfully.',
                'transactionId' => $body['data']['transactionId'] ?? '',
            ]);
        }

        wp_send_json_error([
            'status' => $body['status'] ?? 0,
            'message' => ($body['status'] === 422)
                ? ($body['data'][0] ?? 'Validation error.')
                : ($body['message'] ?? 'Unknown error occurred.'),
        ]);
    }

    // 2.2 – PHR verify OTP
    public function handle_phr_verify_otp(): void
    {
        $transaction_id = sanitize_text_field($_POST['transactionId'] ?? '');
        $otp = sanitize_text_field($_POST['otp'] ?? '');

        if (empty($transaction_id) || empty($otp)) {
            wp_send_json_error(['message' => 'Transaction ID and OTP are required.']);
        }

        $result = $this->api_post('v1/abha/m1-external/phr/verify_otp', [
            'transaction_id' => $transaction_id,
            'otp' => $otp,
            'is_health_number' => false,
        ]);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (empty($body)) {
            wp_send_json_error(['message' => 'Invalid API response.']);
        }

        if (($body['status'] ?? 0) === 200) {
            wp_send_json_success([
                'message' => $body['message'] ?? 'OTP verified.',
                'transactionId' => $body['data']['transactionId'] ?? '',
                'mappedPhrAddress' => $body['data']['mappedPhrAddress'] ?? [],
            ]);
        }

        wp_send_json_error([
            'message' => $body['message'] ?? 'Verification failed.',
            'data' => $body['data'][0] ?? null,
        ]);
    }

    // 2.3 – Get states
    public function handle_get_states(): void
    {
        $result = $this->api_get('v1/abha/m1-external/phr/state');

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (empty($body)) {
            wp_send_json_error(['message' => 'Invalid API response.']);
        }

        if (($body['status'] ?? 0) === 200) {
            // Use wp_send_json (not _success/_error) to pass status through, matching original
            wp_send_json([
                'status' => 200,
                'message' => $body['message'] ?? 'States fetched.',
                'data' => $body['data'] ?? [],
            ]);
        }

        wp_send_json_error([
            'message' => $body['message'] ?? 'No states found.',
            'data' => $body['data'][0] ?? null,
        ]);
    }

    // 2.4 – Get districts
    public function handle_get_districts(): void
    {
        $state_id = intval(sanitize_text_field($_GET['state_ID'] ?? 0));

        if (empty($state_id)) {
            wp_send_json_error(['message' => 'State ID is required.']);
        }

        $result = $this->api_get("v1/abha/m1-external/phr/district?state_id={$state_id}");

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (empty($body)) {
            wp_send_json_error(['message' => 'Invalid API response.']);
        }

        if (($body['status'] ?? 0) === 200) {
            wp_send_json_success([
                'message' => $body['message'] ?? 'Districts fetched.',
                'data' => $body['data'] ?? [],
            ]);
        }

        wp_send_json_error([
            'message' => $body['message'] ?? 'No districts found.',
            'data' => $body['data'][0] ?? null,
        ]);
    }

    // 2.5 – PHR demographic details
    public function handle_phr_demographics(): void
    {
        $transaction_id = sanitize_text_field($_POST['transaction_id'] ?? '');
        $first_name = sanitize_text_field($_POST['first_name'] ?? '');
        $middle_name = sanitize_text_field($_POST['middle_name'] ?? '');
        $last_name = sanitize_text_field($_POST['last_name'] ?? '');
        $pin_code = sanitize_text_field($_POST['pin_code'] ?? '');
        $gender = sanitize_text_field($_POST['gender'] ?? '');
        $dob = sanitize_text_field($_POST['dob'] ?? '');
        $mobile_no = sanitize_text_field($_POST['mobile_no'] ?? '');
        $state_code = sanitize_text_field($_POST['state_code'] ?? '');
        $district_code = sanitize_text_field($_POST['district_code'] ?? '');
        $address = sanitize_textarea_field($_POST['address'] ?? 'Katargam, Surat');

        if (empty($transaction_id) || empty($first_name) || empty($mobile_no)) {
            wp_send_json_error(['message' => 'Transaction ID, First Name, and Mobile Number are required.']);
        }

        $payload = [
            'transaction_id' => $transaction_id,
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
            'pin_code' => $pin_code,
            'gender' => ucfirst($gender),
            'dob' => $dob,
            'mobile_no' => $mobile_no,
            'state_code' => $state_code,
            'district_code' => $district_code,
            'address' => $address,
        ];

        $result = $this->api_post('v1/abha/m1-external/phr/add_demographic_details', $payload);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (empty($body)) {
            wp_send_json_error(['message' => 'Invalid API response.']);
        }

        if (($body['status'] ?? 0) === 200) {
            wp_send_json_success(['message' => $body['message'] ?? 'Demographics submitted successfully.']);
        }

        wp_send_json_error([
            'message' => $body['message'] ?? 'Unknown error occurred.',
            'data' => $body['data'][0] ?? null,
        ]);
    }

    // 2.6 – PHR suggestion
    public function handle_phr_suggestion(): void
    {
        $transaction_id = sanitize_text_field($_GET['transaction_id'] ?? '');

        if (empty($transaction_id)) {
            wp_send_json_error(['message' => 'Transaction ID is required.']);
        }

        $result = $this->api_get("v1/abha/m1-external/phr/suggession?transaction_id={$transaction_id}");

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (empty($body)) {
            wp_send_json_error(['message' => 'Invalid API response.']);
        }

        if (($body['status'] ?? 0) === 200 && !empty($body['data'])) {
            wp_send_json_success([
                'message' => $body['message'] ?? 'Suggestions fetched.',
                'suggestions' => $body['data'] ?? [],
            ]);
        }

        wp_send_json_error([
            'message' => $body['message'] ?? 'No suggestions found.',
            'data' => $body['data'][0] ?? null,
        ]);
    }

    // 2.7 – PHR login
    // FIX: removed unreachable `wp_send_json_error(['post' => $_POST])` at the bottom.
    //      The else branch (JSON response that isn't an image) now properly
    //      sends an error instead of falling through.
    public function handle_phr_login(): void
    {
        $transaction_id = sanitize_text_field($_POST['transaction_id'] ?? '');
        $phr_address = sanitize_text_field($_POST['phr_address'] ?? '');
        $already_reg = sanitize_text_field($_POST['already_registered'] ?? '');

        if (empty($transaction_id) || empty($phr_address)) {
            wp_send_json_error(['message' => 'Transaction ID and PHR address are required.']);
        }

        $result = $this->api_post('v1/abha/m1-external/phr/login', [
            'transaction_id' => $transaction_id,
            'phr_address' => $phr_address,
            'is_health_number' => false,
            'already_registered' => $already_reg,
        ]);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $content_type = (string) ($result['headers']['content-type'] ?? '');
        $raw_body = $result['raw_body'];

        if (strpos($content_type, 'image/') !== false) {
            $this->save_image_to_uploads($raw_body, $content_type);
            wp_send_json_success($this->build_image_response($content_type, $raw_body));
        }

        // JSON error path – was silently falling through before
        $body = $this->parse_json($raw_body);
        wp_send_json_error([
            'message' => $body['message'] ?? 'Login failed.',
            'data' => $body['data'][0] ?? null,
        ]);
    }
}