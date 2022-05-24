<?php

/**
 * WP Mail SMTP
 * https://wordpress.org/plugins/wp-mail-smtp/
 */
class BWFAN_Compatibility_With_WP_SMTP {

	public function __construct() {
		add_action( 'bwfan_before_send_email', array( $this, 'disable_force_email_settings' ) );
	}

	/**
	 * Disable force `from name` & 'from email' setting
	 */
	public static function disable_force_email_settings( $data ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		/** WP SMTP force email setting **/
		$wp_smtp_settings = get_option( 'wp_mail_smtp' );
		if ( empty( $wp_smtp_settings ) || ! is_array( $wp_smtp_settings ) ) {
			return;
		}
		add_filter( 'pre_option_wp_mail_smtp', function ( $value_return ) use ( $wp_smtp_settings ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
			$wp_smtp_settings['mail']['from_email_force'] = false;
			$wp_smtp_settings['mail']['from_name_force']  = false;

			return $wp_smtp_settings;
		}, PHP_INT_MAX );
	}
}

if ( defined( 'WPMS_PLUGIN_VER' ) ) {
	new BWFAN_Compatibility_With_WP_SMTP();
}
