<?php
if ( ! bwfan_is_autonami_pro_active() || version_compare( BWFAN_PRO_VERSION, '2.0.2', '>' ) ) {
	class BWFAN_Contact_City extends BWFAN_Merge_Tag {

		private static $instance = null;

		public function __construct() {
			$this->tag_name        = 'contact_city';
			$this->tag_description = __( 'Contact City', 'wp-marketing-automations' );
			add_shortcode( 'bwfan_contact_city', array( $this, 'parse_shortcode' ) );
			add_shortcode( 'bwfan_customer_city', array( $this, 'parse_shortcode' ) );
		}

		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Parse the merge tag and return its value.
		 *
		 * @param $attr
		 *
		 * @return mixed|string|void
		 */
		public function parse_shortcode( $attr ) {
			$get_data = BWFAN_Merge_Tag_Loader::get_data();
			if ( true === $get_data['is_preview'] ) {
				return $this->parse_shortcode_output( $this->get_dummy_preview(), $attr );
			}

			/** If Contact ID available */
			$cid  = isset( $get_data['contact_id'] ) ? $get_data['contact_id'] : '';
			$city = $this->get_city( $cid );
			if ( ! empty( $city ) ) {
				return $this->parse_shortcode_output( $city, $attr );
			}

			/** If order */
			$order = isset( $get_data['wc_order'] ) ? $get_data['wc_order'] : '';
			if ( bwfan_is_woocommerce_active() && $order instanceof WC_Order ) {
				$city = BWFAN_Woocommerce_Compatibility::get_order_billing_city( $order );
				if ( ! empty( $city ) ) {
					return $this->parse_shortcode_output( $city, $attr );
				}
			}

			/** If user ID or email */
			$user_id = isset( $get_data['user_id'] ) ? $get_data['user_id'] : '';
			$email   = isset( $get_data['email'] ) ? $get_data['email'] : '';

			$contact = bwf_get_contact( $user_id, $email );
			if ( absint( $contact->get_id() ) > 0 && class_exists( 'BWFCRM_Contact' ) ) {
				$contact_crm = new BWFCRM_Contact( $contact );
				$city        = $contact_crm->get_city();
				if ( ! empty ( $city ) ) {
					return $this->parse_shortcode_output( $city, $attr );
				}
			}

			return $this->parse_shortcode_output( '', $attr );
		}

		public function get_city( $cid ) {
			$cid = absint( $cid );
			if ( 0 === $cid ) {
				return '';
			}
			if ( ! bwfan_is_autonami_pro_active() ) {
				return '';
			}
			$contact = new WooFunnels_Contact( '', '', '', $cid );
			if ( $contact->get_id() > 0 ) {
				$contact_crm = new BWFCRM_Contact( $contact );

				return $contact_crm->get_city();
			}

			return '';
		}

		/**
		 * Show dummy value of the current merge tag.
		 *
		 * @return string
		 */
		public function get_dummy_preview() {
			$contact = $this->get_contact_data();
			$city    = 'New York';
			/** check for contact instance and the contact id */
			if ( ! $contact instanceof WooFunnels_Contact || 0 === absint( $contact->get_id() ) ) {
				return $city;
			}

			if ( ! bwfan_is_autonami_pro_active() ) {
				return $city;
			}

			/** check if empty */
			$contact_crm = new BWFCRM_Contact( $contact );
			if ( empty( $contact_crm->get_city() ) ) {
				return $city;
			}

			return $contact_crm->get_city();
		}
	}

	/**
	 * Register this merge tag to a group.
	 */
	BWFAN_Merge_Tag_Loader::register( 'bwf_contact', 'BWFAN_Contact_City' );
}
