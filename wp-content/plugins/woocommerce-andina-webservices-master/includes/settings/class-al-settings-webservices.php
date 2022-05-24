<?php
/**
 * Woocommerce Andina Licores Webservices - Settings
 *
 * @package AutomaticSynchronizationSAP
 * @version 1.0.0
 * @since   1.0.0
 * @author  ComprasEC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'AL_Webservices_Settings' ) ) :

	/**
	 * AL_Webservices_Settings Class
	 *
	 * @class   AL_Webservices_Settings
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	class AL_Webservices_Settings extends WC_Settings_Page {
		/**
		 * Constructor.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->id    = 'al_andina_licores_webservices';
			$this->label = __( 'Webservices Andina', 'al-andina-licores-webservices' );
			parent::__construct();
		}

		/**
		 * Get_settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function get_settings() {
			global $current_section;
			return apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() );
		}

		/**
		 * Maybe_reset_settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function maybe_reset_settings() {
			global $current_section;
			if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
				foreach ( $this->get_settings() as $value ) {
					if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
						delete_option( $value['id'] );
						$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
						add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
					}
				}
			}
		}

		/**
		 * Save settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function save() {
			parent::save();
			$this->maybe_reset_settings();
		}

	}

endif;

return new AL_Webservices_Settings();
