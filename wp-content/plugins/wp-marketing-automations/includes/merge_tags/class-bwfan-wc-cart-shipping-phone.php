<?php

class BWFAN_WC_Cart_Shipping_Phone extends Cart_Merge_Tag {

	private static $instance = null;

	public function __construct() {
		$this->tag_name        = 'cart_shipping_phone';
		$this->tag_description = __( 'Cart Shipping Phone', 'wp-marketing-automations' );
		add_shortcode( 'bwfan_cart_shipping_phone', array( $this, 'parse_shortcode' ) );
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
		if ( true === BWFAN_Merge_Tag_Loader::get_data( 'is_preview' ) ) {
			return $this->get_dummy_preview();
		}

		$cart_details     = BWFAN_Merge_Tag_Loader::get_data( 'cart_details' );
		$shipping_phone   = $this->get_cart_value( 'shipping_phone', $cart_details );
		$shipping_country = $this->get_cart_value( 'shipping_country', $cart_details );

		if ( ! empty( $shipping_country ) ) {
			$shipping_phone = BWFAN_Phone_Numbers::add_country_code( $shipping_phone, $shipping_country );
		}

		return $this->parse_shortcode_output( $shipping_phone, $attr );
	}

	/**
	 * Show dummy value of the current merge tag.
	 *
	 * @return string
	 */
	public function get_dummy_preview() {
		return '+919999999999';
	}


}

/**
 * Register this merge tag to a group.
 */
if ( bwfan_is_woocommerce_active() ) {
	BWFAN_Merge_Tag_Loader::register( 'wc_ab_cart', 'BWFAN_WC_Cart_Shipping_Phone' );
}
