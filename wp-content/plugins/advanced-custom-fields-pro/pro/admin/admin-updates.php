<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'ACF_Admin_Updates' ) ) :

	class ACF_Admin_Updates {

		/** @var array Data used in the view. */
		var $view = array();

		/**
		 * __construct
		 *
		 * Sets up the class functionality.
		 *
		 * @date    23/06/12
		 * @since   5.0.0
		 *
		 * @param   void
		 * @return  void
		 */
		function __construct() {

			// Add actions.
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 20 );
		}

		/**
		 * display_wp_error
		 *
		 * Adds an admin notice using the provided WP_Error.
		 *
		 * @date    14/1/19
		 * @since   5.7.10
		 *
		 * @param   WP_Error $wp_error The error to display.
		 * @return  void
		 */
		function display_wp_error( $wp_error ) {

			// Only show one error on page.
			if ( acf_has_done( 'display_wp_error' ) ) {
				return;
			}

			// Create new notice.
			acf_new_admin_notice(
				array(
					'text' => __( '<strong>Error</strong>. Could not connect to the update server', 'acf' ) . ' <span class="description">(' . esc_html( $wp_error->get_error_message() ) . ').</span>',
					'type' => 'error',
				)
			);
		}

		/**
		 * get_changelog_changes
		 *
		 * Finds the specific changes for a given version from the provided changelog snippet.
		 *
		 * @date    14/1/19
		 * @since   5.7.10
		 *
		 * @param   string $changelog The changelog text.
		 * @param   string $version The version to find.
		 * @return  string
		 */
		function get_changelog_changes( $changelog = '', $version = '' ) {

			// Explode changelog into sections.
			$bits = array_filter( explode( '<h4>', $changelog ) );

			// Loop over each version chunk.
			foreach ( $bits as $bit ) {

				// Find the version number for this chunk.
				$bit         = explode( '</h4>', $bit );
				$bit_version = trim( $bit[0] );
				$bit_text    = trim( $bit[1] );

				// Compare the chunk version number against param and return HTML.
				if ( acf_version_compare( $bit_version, '==', $version ) ) {
					return '<h4>' . esc_html( $bit_version ) . '</h4>' . acf_esc_html( $bit_text );
				}
			}

			// Return.
			return '';
		}

		/**
		 * admin_menu
		 *
		 * Adds the admin menu subpage.
		 *
		 * @date    28/09/13
		 * @since   5.0.0
		 *
		 * @param   void
		 * @return  void
		 */
		function admin_menu() {

			// Bail early if no show_admin.
			if ( ! acf_get_setting( 'show_admin' ) ) {
				return;
			}

			// Bail early if no show_updates.
			if ( ! acf_get_setting( 'show_updates' ) ) {
				return;
			}

			// Bail early if not a plugin (included in theme).
			if ( ! acf_is_plugin_active() ) {
				return;
			}

			// Add submenu.
			$page = add_submenu_page( 'edit.php?post_type=acf-field-group', __( 'Updates', 'acf' ), __( 'Updates', 'acf' ), acf_get_setting( 'capability' ), 'acf-settings-updates', array( $this, 'html' ) );

			// Add actions to page.
			add_action( "load-$page", array( $this, 'load' ) );
		}

		/**
		 * load
		 *
		 * Runs when loading the submenu page.
		 *
		 * @date    7/01/2014
		 * @since   5.0.0
		 *
		 * @param   void
		 * @return  void
		 */
		function load() {

			add_action( 'admin_body_class', array( $this, 'admin_body_class' ) );

			// Check activate.
			if ( acf_verify_nonce( 'activate_pro_license' ) ) {
				acf_pro_activate_license( sanitize_text_field( $_POST['acf_pro_license'] ) );

				// Check deactivate.
			} elseif ( acf_verify_nonce( 'deactivate_pro_license' ) ) {
				acf_pro_deactivate_license();
			}

			// vars
			$license    = acf_pro_get_license_key();
			$this->view = array(
				'license'            => $license,
				'license_status'     => acf_pro_get_license_status( ! empty( $_GET['acf-recheck-license'] ) ),
				'active'             => $license ? 1 : 0,
				'current_version'    => acf_get_setting( 'version' ),
				'remote_version'     => '',
				'update_available'   => false,
				'changelog'          => '',
				'upgrade_notice'     => '',
				'is_defined_license' => defined( 'ACF_PRO_LICENSE' ) && ! empty( ACF_PRO_LICENSE ) && is_string( ACF_PRO_LICENSE ),
				'license_error'      => false,
				'wp_not_compatible'  => false,
			);

			// get plugin updates
			$force_check = ! empty( $_GET['force-check'] );
			$info        = acf_updates()->get_plugin_info( 'pro', $force_check );

			// Display error.
			if ( is_wp_error( $info ) ) {
				return $this->display_wp_error( $info );
			}

			// add info to view
			$this->view['remote_version'] = $info['version'];

			// add changelog if the remote version is '>' than the current version
			$version = acf_get_setting( 'version' );

			// check if remote version is higher than current version
			
		}

		/**
		 * Modifies the admin body class.
		 *
		 * @since 6.0.0
		 *
		 * @param string $classes Space-separated list of CSS classes.
		 * @return string
		 */
		public function admin_body_class( $classes ) {
			$classes .= ' acf-admin-page';
			return $classes;
		}

		/**
		 * html
		 *
		 * Displays the submenu page's HTML.
		 *
		 * @date    7/01/2014
		 * @since   5.0.0
		 *
		 * @param   void
		 * @return  void
		 */
		function html() {
			acf_get_view( dirname( __FILE__ ) . '/views/html-settings-updates.php', $this->view );
		}
	}

	// Initialize.
	acf_new_instance( 'ACF_Admin_Updates' );

endif; // class_exists check