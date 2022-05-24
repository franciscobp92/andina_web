<?php

class BWFAN_WC_Cart_Items extends Merge_Tag_Abstract_Product_Display {

	private static $instance = null;

	public $supports_cart_table = true;

	public function __construct() {
		$this->tag_name        = 'cart_items';
		$this->tag_description = __( 'Cart Items', 'wp-marketing-automations' );
		add_shortcode( 'bwfan_cart_items', array( $this, 'parse_shortcode' ) );
		$this->support_fallback = false;
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
		if ( false !== BWFAN_Merge_Tag_Loader::get_data( 'is_preview' ) ) {
			$args = array(
				'posts_per_page' => 1,
				'orderby'        => 'rand',
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'fields'         => 'ids',
			);

			$random_products = get_posts( $args );
			$products        = [];
			foreach ( $random_products as $product ) {
				if ( absint( $product ) > 0 ) {
					$products[] = wc_get_product( $product );
				}
			}
			$this->products = $products;
			$result         = $this->process_shortcode( $attr );

			return $this->parse_shortcode_output( $result, $attr );
		}

		$cart_details     = BWFAN_Merge_Tag_Loader::get_data( 'cart_details' );
		$checkout_data    = isset( $cart_details['checkout_data'] ) ? $cart_details['checkout_data'] : '';
		$checkout_data    = json_decode( $checkout_data, true );
		$lang             = is_array( $checkout_data ) && isset( $checkout_data['lang'] ) ? $checkout_data['lang'] : '';
		$items            = apply_filters( 'bwfan_abandoned_cart_items_visibility', maybe_unserialize( $cart_details['items'] ) );
		$products         = [];
		$product_quantity = [];
		foreach ( $items as $item ) {
			if ( ! $item['data'] instanceof WC_Product ) {
				continue;
			}
			$products[] = $item['data'];

			$product_quantity[ $item['data']->get_id() ] = $item['quantity'];
		}

		$this->cart              = $items;
		$this->products_quantity = $product_quantity;
		$this->data              = [
			'coupons'            => maybe_unserialize( $cart_details['coupons'] ),
			'fees'               => maybe_unserialize( $cart_details['fees'] ),
			'shipping_total'     => maybe_unserialize( $cart_details['shipping_total'] ),
			'shipping_tax_total' => maybe_unserialize( $cart_details['shipping_tax_total'] ),
			'total'              => maybe_unserialize( $cart_details['total'] ),
			'currency'           => maybe_unserialize( $cart_details['currency'] ),
			'lang'               => $lang
		];
		$this->products          = $products;

		$result = $this->process_shortcode( $attr );

		return $this->parse_shortcode_output( $result, $attr );
	}


}

/**
 * Register this merge tag to a group.
 *
 */
if ( bwfan_is_woocommerce_active() ) {
	BWFAN_Merge_Tag_Loader::register( 'wc_ab_cart', 'BWFAN_WC_Cart_Items' );
}