<?php
/**
 * Woocommerce Andina Licores Webservices  - Connection Webservice
 *
 * @package WebservicesAndinaLicores
 * @version 1.0.0
 * @since   1.0.0
 * @author  ComprasEC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'AL_Connection_Webservice' ) ) :

	/**
	 * AL_Connection_Webservice Class
	 *
	 * @class   AL_Connection_Webservice
	 * @version 1.0.0
	 * @since   1.0.0
	 */
    class AL_Connection_Webservice extends Andina_licores_User
    {   
		/**
		 * Soap Call method to get response from Webservice.
		 *
		 * @version  	1.0.0
		 * @since   	1.0.0
		 * @param		array  | Params 
		 * @param		string | RFC for make Soap Call  
		 * @return 		object	
		 */
		protected function newSoapCallClient(array $params, string $rfc) 
		{
		    $this->custom_logs("-----------------------------------------------------");
		    $this->custom_logs($rfc);
		    $this->custom_logs($params);
		    $this->custom_logs("-----------------------------------------------------");
			try {
				$client = new SoapClient( get_option("al_andina_licores_webservices_url"), $this->soapConfig() );
				return $client->__soapCall( $rfc, array( $params ) );
			} catch (Exception $e) {
				$this->custom_logs("Descripcion de error en conexion con webservice:" . "\n" . $e->getMessage() ."\n");
				return false;
			}
		}

		/**
		 * Soap Config.
		 * @version  	1.0.0
		 * @since   	1.0.0
		 * @param		string | Url Location for Config Soap 
		 * @return 		array	
		 */
		private function soapConfig() 
		{
			return array(
				'exceptions'     => true,
				'trace'          => true,
			);
		}
		
		/**
		 * Custom logs.
		 * @version  	1.0.0
		 * @since   	1.0.0
		 * @param		string | Message	
		 */
		protected function custom_logs( $message ) 
		{ 
			if( is_array( $message ) || is_object($message) ) { 
				$message = json_encode($message); 
			} 
			$file = fopen( "./andina_licores.log","a" ); 
			fwrite( $file, "\n" . date('Y-m-d h:i:s') . " :: " . $message ); 
			fclose($file);
		}

		/**
		 * Custom notification logs by email
		 * @version  1.0.0
		 * @since	 1.0.0
		 * @param	 string|array $message 
		 */
		protected function sendCustomNotification( $message )
		{
			try {
				if( is_array( (array) $message ) ) { 
					$message = json_encode($message); 
				}
				$to      	= get_option("al_andina_licores_notifications_emails");
				$title    	= 'Logs andina licores b2b';
				$message 	= wordwrap($message, 70, "\r\n");
				$headers 	= 'From: andina@andinalicores.com.ec' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
	
				mail($to, $title, $message, $headers);
			} catch (\Exception $e) {
				return false;
			}
    	}
    }
endif;