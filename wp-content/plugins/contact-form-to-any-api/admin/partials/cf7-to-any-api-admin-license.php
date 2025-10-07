<?php 
$page_heading = 'Activate Your License Key';
$input_label = 'Activate License';
$screen = get_current_screen();

if ($screen->base === 'cf7_to_any_api_page_cf7anyapi_license_setting') {
	$page_heading = 'Your License Key';
	$input_label = 'License Key';
}

$license_key = get_option('cf7_to_any_api_license_key'); 

$license_status = "Inactive";
$license_class = "inactive";
$license_type = "-";
$version = "-";
$release_date = "-";
$changelog = [];

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
        $license_type = !empty($data['license_type']) ? $data['license_type'] : "-";
        $version = !empty($data['version']) ? $data['version'] : "-";
        $release_date = !empty($data['release_date']) ? $data['release_date'] : "-";
        $changelog = !empty($data['changelog']) ? (is_array($data['changelog']) ? implode("<br>", $data['changelog']) : $data['changelog']) : "";
    }
}

$cf7anyapi_object = new Cf7_To_Any_Api();
$cf7anyapi_options = $cf7anyapi_object->license_get_options();?>
<div class="wrap cf-settings-wrap cf-updates">
	<div class="cfl-box">

		<!-- Licence Information -->

		<div class="title-wrap">
			<h3><img src="<?php echo esc_url( plugin_dir_url( __DIR__ ).'images/icon-key.svg');?>" alt="key"><span><?php esc_html_e( $page_heading, 'contact-form-to-any-api' ); ?></span></h3>
		</div>

		<div class="licence-wrap">
			<table class="cf7anyapi_license_tbl">
				<tr class="cf7anyapi_tbl_row">
					<td class="cf7anyapi_tbl_data"><label for="cf7anyapi_license"><?php esc_html_e( $input_label, 'contact-form-to-any-api' ); ?></label></td>
					<?php 
						if ($screen->base === 'cf7_to_any_api_page_cf7anyapi_license_setting') { ?>
					<td class="cf7anyapi_tbl_license_input"><input id="cf7anyapi_license" class="cf7anyapi-license-input" disabled type="password" placeholder="<?php esc_attr_e( 'Activate License', 'contact-form-to-any-api' ); ?>" value="<?php echo esc_attr($cf7anyapi_options['cf7_to_any_api_license_key']); ?>"></td>		
					<?php }else{ ?>
					<td class="cf7anyapi_tbl_license_input"><input id="cf7anyapi_license" class="cf7anyapi-license-input" type="text" placeholder="<?php esc_attr_e( 'License Key', 'contact-form-to-any-api' ); ?>"></td>
					<?php } ?>
				</tr>
				<tr>
					<td class="loader_wrap">
						<?php 
						if ($screen->base === 'cf7_to_any_api_page_cf7anyapi_license_setting') { ?>
						<input type="submit" id="cf7anyapi_deactivate_license" name="cf7anyapi_license_submit" value="<?php esc_attr_e( 'Deactivate', 'contact-form-to-any-api' ); ?>" class="button-primary">
						<?php }else{ 
						?>
						<input type="submit" id="cf7anyapi_activate_license" name="cf7anyapi_license_submit" value="<?php esc_attr_e( 'Activate', 'contact-form-to-any-api' ); ?>" class="button-primary">
						<?php } ?>
						<img class="cf7anyapi_activate_license_loader" src="<?php echo plugin_dir_url( __DIR__ ); ?>images/loader.gif" style="display: none;">

					</td>
					<td><a href="<?php echo CF7_CURL_DOMAIN;?>/my-account/" target="_blank" class="cf-manage-license-btn">Manage License <span class="dashicons dashicons-arrow-right-alt"></span></a></td>
				</tr>
				<tr>
					<td><p class="cf7anyapi_activate_license_error"></p></td>
				</tr>
			</table>
		</div>

	
		<div class="cf-license-status-wrap" bis_skin_checked="1">			
			<table class="cf-license-status-table">
				<tbody>
					<tr>
						<th>License Status</th>
						<td><span class="cf-license-status <?php echo esc_attr($license_class); ?>"><?php echo esc_html($license_status); ?></span></td>
					</tr>
					<tr>
		                <th>License Type</th>
		                <td><span class="cf-license-type"><?php echo esc_html($license_type); ?></span></td>
		            </tr>
				</tbody>
			</table>			
		</div>

		<div class="cf-no-license-view-pricing" bis_skin_checked="1">
			<span> 
				Enable OAuth 2.0 Customization for CF7?  <a href="<?php echo CF7_CURL_DOMAIN;?>/pricing/#oauth" target="_blank">View pricing &amp; purchase <span class="dashicons dashicons-arrow-right-alt"></span></a>
			</span>
		</div>
	</div>
	<div class="cfl-box w-35">

		<!-- Update Information -->

		<div class="title-wrap">
			<h3><img src="<?php echo esc_url( plugin_dir_url( __DIR__ ).'images/icon-info.svg');?>" alt="key"><span><?php esc_html_e( 'Update Information', 'contact-form-to-any-api' ); ?></span></h3>
		</div>

		<div class="updates-wrap">
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label>Current Version</label>
						</th>
						<td><?php echo esc_html(CF7_TO_ANY_API_VERSION); ?></td>
					</tr>
					<tr>
						<th>
							<label>Latest Version</label>
						</th>
						<td><?php echo esc_html($version); ?></td>
					</tr>
					<tr>
						<th>
							<label>Update Available</label>
						</th>
						<td><span style="margin-right: 5px;"><?php echo ($version > CF7_TO_ANY_API_VERSION) ? "Yes" : "You are using Latest Version"; ?></span></td>

						<?php 
						if($version > CF7_TO_ANY_API_VERSION ){
							$updater_url = add_query_arg( array( 'cf7api_check_for_updates' => 1, 'cf7api_nonce' => wp_create_nonce( 'cf7api_check_for_updates' ) ), network_admin_url( 'plugins.php' ) ); ?>
							<td><a class="cf7-secondary" href="<?php echo $updater_url;?>">Check for update</a></td><?php 
						} ?>
					</tr>
				</tbody>
			</table>
			<div class="cf-update-changelog">
				<h4><?php echo esc_html($version); ?></h4>
				<p><em>Release Date <?php echo esc_html($release_date); ?></em></p>
				<?php
		        if (!empty($changelog)) {
				    if (is_array($changelog)) {
				        echo "<ul>";
				        foreach ($changelog as $log) {
				            echo "<li>" . esc_html($log) . "</li>";
				        }
				        echo "</ul>";
				    } else {
				        echo $changelog;
				    }
				} else {
				    echo "<p>No changelog available.</p>";
				} ?>
			</div>
		</div>
	</div>
</div>