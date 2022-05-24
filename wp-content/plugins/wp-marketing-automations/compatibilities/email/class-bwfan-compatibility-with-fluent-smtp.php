<?php

/**
 * FluentSMTP
 * https://wordpress.org/plugins/fluent-smtp/
 */
class BWFAN_Compatibility_With_Fluent_SMTP {

	public function __construct() {
		add_action( 'bwfan_before_send_email', array( $this, 'disable_force_email_settings' ) );
	}

	/**
	 * Disable force `from name` & 'from email' setting
	 */
	public static function disable_force_email_settings( $data ) {
		if ( ! isset( $data['from_email'] ) && ! isset( $data['senders_email'] ) ) {
			return;
		}

		/** Fluent SMTP force email setting **/
		$fluent_smtp_settings = get_option( 'fluentmail-settings' );
		if ( empty( $fluent_smtp_settings ) || ! is_array( $fluent_smtp_settings ) ) {
			return;
		}

		add_filter( 'pre_option_fluentmail-settings', function ( $value_return ) use ( $fluent_smtp_settings, $data ) {
			$from_email = isset( $data['senders_email'] ) ? $data['senders_email'] : $data['from_email'];
			if ( empty( $from_email ) ) {
				return $value_return;
			}

			$default_connection = isset( $fluent_smtp_settings['misc']['default_connection'] ) ? $fluent_smtp_settings['misc']['default_connection'] : '';
			if ( empty( $default_connection ) ) {
				return $value_return;
			}
			$fluent_smtp_settings['connections'][ $default_connection ]['provider_settings']['sender_email']    = $from_email;
			$fluent_smtp_settings['connections'][ $default_connection ]['provider_settings']['force_from_name'] = 'no';

			return $fluent_smtp_settings;
		}, PHP_INT_MAX );
	}
}

if ( defined( 'FLUENTMAIL' ) ) {
	new BWFAN_Compatibility_With_Fluent_SMTP();
}
