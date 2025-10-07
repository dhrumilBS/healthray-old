<?php 
global $wp_version;
$cf7anyapi_options = Cf7_To_Any_Api::license_get_options();

$license_key = get_option('cf7_to_any_api_license_key'); 

$license_status = "Inactive";
$license_class = "inactive";
$license_type = "N/A";
$version = "Unknown";
$release_date = "Unknown";
$changelog = [];
$server_status = 'Disconnected';

if (!empty($license_key)) {
    $url = CF7_CURL_DOMAIN . '/wp-json/cf7api/info';

    $data_array = array(
        'site_url' => get_site_url(),
        'license_key' => $license_key
    );

    $response = wp_remote_post($url, array(
        'body' => $data_array,
        'timeout' => 20,
        'sslverify' => true
    ));

    if (is_wp_error($response)) {
        $error = $response->get_error_message() ? $response->get_error_message() : "Sorry, something went wrong. Please try again.";  
    } else {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Assigning values from API response
        $license_status = !empty($data['license_status']) ? ucfirst($data['license_status']) : "Inactive";
        $license_class = ($license_status === "Active") ? "active" : "inactive";
        $license_type = !empty($data['license_type']) ? $data['license_type'] : "N/A";
        $version = !empty($data['version']) ? $data['version'] : "Unknown";
        $release_date = !empty($data['release_date']) ? $data['release_date'] : "Unknown";
        $changelog = !empty($data['changelog']) ? (is_array($data['changelog']) ? implode("<br>", $data['changelog']) : $data['changelog']) : "";
        $server_status = !empty($data) ? "Connected to Server" : "Not Connected";
    }
}
$page_heading = 'System Status';?>
<div class="wrap cf-settings-wrap cf-sys-status">
	<nav class="nav-tab-wrapper cf-tab-wrapper">
        <a href="<?php echo esc_url(admin_url('edit.php?post_type=cf7_to_any_api&page=cf7anyapi_setting')); ?>" class="nav-tab">Settings</a>
        <a href="<?php echo esc_url(admin_url('edit.php?post_type=cf7_to_any_api&page=cf7anyapi_setting&tab=status')); ?>" class="nav-tab nav-tab-active">System Status</a>
    </nav>

	<div class="cfs-box">
		<div class="title-wrap">
			<h3><img src="<?php echo esc_url( plugin_dir_url( __DIR__ ).'images/check.png');?>" alt="key"><span><?php esc_html_e( $page_heading, 'contact-form-to-any-api' ); ?></span></h3>
		</div>
		<div class="status-wrap">
			<table class="table status-table widefat" width="100%">
				<tbody>
					<tr>
						<td><?php esc_html_e( 'Site URL', 'contact-form-to-any-api' ); ?></td>
						<td><?php echo esc_url(get_site_url()); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'PHP Version', 'contact-form-to-any-api' ); ?></td>
						<td><?php echo esc_html(phpversion()); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'SSL Status', 'contact-form-to-any-api' ); ?></td>
						<td><?php echo is_ssl() ? 'Enabled' : 'Disabled'; ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e('Server Connection', 'contact-form-to-any-api'); ?></td>
	                    <td><strong style="color: <?php echo ($server_status === 'Connected to Server') ? 'green' : 'red'; ?>">
	                        <?php echo esc_html($server_status); ?>
	                    </strong></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'License Key', 'contact-form-to-any-api' ); ?></td>
						<td><?php echo $cf7anyapi_options['cf7_to_any_api_license_key'] ? '************************************************************************' : '-'; ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'License Status', 'contact-form-to-any-api' ); ?></td>
						<td class="<?php echo esc_attr($license_class); ?>">
							<strong style="color: <?php echo ($license_status === 'Active') ? 'green' : 'red'; ?>"> 
								<?php echo esc_html($license_status); ?>
							</strong>
						</td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'License Type', 'contact-form-to-any-api' ); ?></td>
						<td><?php echo esc_html($license_type); ?></td>
					</tr>
					<?php 
					$response = wp_remote_get(CF7_CURL_DOMAIN);?>
					<tr>
						<td><?php esc_html_e( 'REST API Status', 'contact-form-to-any-api' ); ?></td>
						<td><?php echo ( $response ? 'Enabled' : 'Disabled'); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'My Account', 'contact-form-to-any-api' ); ?></td>
						<td><a href="<?php echo CF7_CURL_DOMAIN;?>/my-account/" target="_blank"><?php esc_html_e( 'Go My Account', 'contact-form-to-any-api' ); ?></a></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>