<?php
/**
 * Woocommerce Andina Licores Webservices - Section Settings
 *
 * @package WebservicesAndinaLicores
 * @version 1.0.0
 * @since   1.0.0
 * @author  ComprasEC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Al_Webservices_Settings_Section' ) ) :

	/**
	 * Al_Webservices_Settings_Section Class
	 *
	 * @class   Al_Webservices_Settings_Section
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	class Al_Webservices_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function __construct() {
			add_filter( 'woocommerce_get_sections_al_andina_licores_webservices', array( $this, 'settings_section' ) );
			add_filter( 'woocommerce_get_settings_al_andina_licores_webservices_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
		}

		/**
		 * Settings_section.
		 *
		 * @param array $sections Section for Settings.
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function settings_section( $sections ) {
			$sections[ $this->id ] = $this->desc;
			return $sections;
		}

		/**
		 * Get_settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function get_settings() {
			return array_merge(
				$this->get_section_settings(),
				array(
					array(
						'title' => __( 'Reset Settings', 'al-andina-licores-webservices' ),
						'type'  => 'title',
						'id'    => 'al_andina_licores_webservices' . $this->id . '_reset_options',
					),
					array(
						'title'   => __( 'Reset section settings', 'al-andina-licores-webservices' ),
						'desc'    => '<strong>' . __( 'Reset', 'al-andina-licores-webservices' ) . '</strong>',
						'id'      => 'al_andina_licores_webservices' . $this->id . '_reset',
						'default' => 'no',
						'type'    => 'checkbox',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'al_andina_licores_webservices' . $this->id . '_reset_options',
					),
				)
			);
		}

	}

endif;
