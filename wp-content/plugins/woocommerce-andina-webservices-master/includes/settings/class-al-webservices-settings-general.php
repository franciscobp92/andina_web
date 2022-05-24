<?php
/**
 * Woocommerce Andina Licores Webservices - General Section Settings
 *
 * @package WebservicesAndinaLicores
 * @version 1.0.0
 * @since   1.0.0
 * @author  ComprasEC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Al_Webservices_Settings_General' ) ) :

	/**
	 * Al_Webservices_Settings_General Class
	 *
	 * @class   Al_Webservices_Settings_General
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	class Al_Webservices_Settings_General extends Al_Webservices_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->id   = '';
			$this->desc = __( 'General', 'al-andina-licores-webservices' );
			parent::__construct();
		}

		/**
		 * Get_section_settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function get_section_settings() {
			$settings = array(
				array(
					'title' => __( 'Configuración general', 'al-andina-licores-webservices' ),
					'type'  => 'title',
					'id'    => 'al_andina_licores_webservices_options',
				),
				array(
					'title'    => __( 'Habilitar/Deshabilitar', 'al-andina-licores-webservices' ),
					'desc'     => '<strong>' . __( 'Activar/Desactivar plugin', 'al-andina-licores-webservices' ) . '</strong>',
					'id'       => 'al_andina_licores_webservices_enabled',
					'default'  => 'yes',
					'type'     => 'checkbox',
				),
				array(
					'title'    => __( 'Notificación de errores', 'al-andina-licores-webservices' ),
					'desc'     => '<strong>' . __( 'Listado de correos electronicos a los cuales se reportaran los errores, se deben ingresar separados por coma, por ejemplo: example@mail.com, example2@mail.com', 'al-andina-licores-webservices' ) . '</strong>',
					'id'       => 'al_andina_licores_notifications_emails',
					'type'     => 'text',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'al_andina_licores_webservices_options',
				),
                array(
					'title' => __( 'Webservice URL', 'al-andina-licores-webservices' ),
					'type'  => 'title',
					'id'    => 'al_andina_licores_webservices_endpoints',
				),
				array(
					'title'    => __( 'URL', 'al-andina-licores-webservices' ),
					'desc'     => '<strong>' . __( 'URL del webservice de Andina Licores ', 'al-andina-licores-webservices' ) . '</strong>',
					'id'       => 'al_andina_licores_webservices_url',
					'type'     => 'text',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'al_andina_licores_webservices_endpoints',
				),
			);
			return $settings;
		}

	}

endif;

return new Al_Webservices_Settings_General();
