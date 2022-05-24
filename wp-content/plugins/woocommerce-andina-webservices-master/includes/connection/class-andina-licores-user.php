<?php
/**
 * Woocommerce Andina Licores Webservices  - User
 *
 * @package WebservicesAndinaLicores
 * @version 1.0.0
 * @since   1.0.0
 * @author  ComprasEC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Andina_licores_User' ) ) :
    
    /**
	 * Andina_Licores_User Class
	 *
	 * @class   Andina_licores_User
	 * @version 1.0.0
	 * @since   1.0.0
	 */
    class Andina_licores_User
    {
        /**
		 * Constructor.
		 * @param   int $idUSer
         * @param   array $roles
		 * @version 1.0.0
		 * @since   1.0.0
		 */
        public function __construct(int $idUser, array $roles)
        {
            $this->user = get_user_meta($idUser);
            $this->rol = $roles[0];
            $this->idUser = $idUser;
        }

        /**
         * This function is used to obtain ther user's id
         * @return int 
         */
        public function get_user_id()
        {
            return $this->idUser;
        }
        /**
         * This function is used to obtain the user's roles
         * @return string 
         */
        public function get_role()
        {
            return $this->rol;
        }
        /**
         * This function is used to obtain the the user's list code.
         * @return string
         */
        public function get_user_list()
        {
            return !empty($this->user['_cli_lista']) ? $this->user['_cli_lista'][0] : "";
        }

        /**
         * This function is used to obtain the the user's cli code.
         * @return string
         */
        public function get_cli_codigo()
        {
            return !empty($this->user['_cli_codigo']) ? $this->user['_cli_codigo'][0] : "";
        }

        /**
         * This function is used to obtain the user's warehouse cli.
         * @return string
         */
        public function get_cli_almacen()
        {
            return !empty($this->user['_cli_almacen']) ? $this->user['_cli_almacen'][0] : "";
        }

        /**
         * This function is used to obtain the user's store cli.
         * @return string
         */
        public function get_cli_bodega()
        {
            return !empty($this->user['_cli_bodega']) ? $this->user['_cli_bodega'][0] : "";
        }

        /**
         * This function is used to obtain the user's pventa cli.
         * @return string
         */
        public function get_cli_pventa()
        {
            return !empty($this->user['_cli_pventa']) ? $this->user['_cli_pventa'][0] : 0;
        }

        /**
         * This function is used to obtain the user's route cli.
         * @return string
         */
        public function get_cli_ruta()
        {
            return !empty($this->user['_cli_ruta']) ? $this->user['_cli_ruta'][0] : "";
        }
        /**
         * This function is used to obtain the user's available balance.
         */
        public function get_cli_saldo_disponible()
        {
            return !empty($this->user['_cli_saldo_disponible']) ? $this->user['_cli_saldo_disponible'][0] : 0;
        }

        /**
         * This function is used to obtain all user's addresses
         * @return array
         */
        public function get_addresses()
        {
            $array = [];
            if (!empty( $this->user['_cli_direccion'] ) ) {
                for( $i=0; $i < count( $this->user['_cli_direccion'] ); $i++ ) {
                    if ( !empty( $this->user['_cli_direccion'][$i] ) ) {
                        $address= explode("?", $this->user['_cli_direccion'][$i]);
                        $values = (object) [
                            "codigo_dir"        => $address[0],
                            "cld_direccion"     => $address[1],
                            "cli_almacen"       => $address[2],
                            "cli_bodega"        => $address[3],
                            "cli_pventa"        => $address[4],
                            "cli_ciudad"        => $address[5],
                            "cli_provincia"     => str_replace(";", "", $address[6])
                        ];
                        array_push($array, $values);
                    }
                }
            }
            return $array;
        }

        /**
         * This function is used to obtaion the cli code of the seller user.
         * @return int
         */
        public function get_cli_codigo_vend()
        {
            return $this->user['_cli_codigo_vend'][0];
        }

        /**
         * This function is used to obtaion the cli id of the seller user.
         * @return int
         */
        public function get_cli_id()
        {
            return !empty($this->user['_cli_id']) ? $this->user['_cli_id'][0] : "";
        }

        /**
         * This function is used to obtaion the cli code address of user.
         * @return int
         */
        public function get_code_dir()
        {
            return !empty($this->user['_cli_dir']) ? $this->user['_cli_dir'][0] : false;
        } 
        
		/**
		 * @version   1.0.0
         * @since     1.0.0
		 * @param string Codigo de Bodega
		 * 300 - 49 -  BODEGA MAY. GUAYAQUIL
		 * 301 - 50 -  BODEGA MAY. CUENCA
		 * 302 - 51 -  BODEGA MAY. QUITO
		 * 306 - 52 -  BODEGA MAY. MANTA
		 * 307 - 53 -  BODEGA MAY. AMBATO
		 * 308 - 54 -  BODEGA MAY. STO.DOMINGO
		 * 311 - 57 -  BODEGA MAY. LOJA
		 * 312 - 58 -  BODEGA MAY. MACHALA
		 * @return string
		 */
		public function get_store_cli_code($store_code) 
		{
			switch ($store_code) {
				case '300':
					return "49";
					break;
				case '301':
					return "50";
					break;
				case '302':
					return "51";
					break;
				case '306':
					return "52";
					break;
				case '307':
					return "53";
					break;
				case '308':
					return "54";
					break;
				case '311':
					return "57";
					break;
				case '312':
					return "58";
					break;
			}
		}
    }
    

endif;