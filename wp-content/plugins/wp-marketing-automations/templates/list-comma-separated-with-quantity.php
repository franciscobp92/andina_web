<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** checking if woocommerce exists other wise return */
if ( ! function_exists( 'bwfan_is_woocommerce_active' ) || ! bwfan_is_woocommerce_active() ) {
	return;
}

$product_names_with_quantity = [];
foreach ( $products as $product ) {
	$product_quantity = '';
	if ( ! $product instanceof WC_Product ) {
		continue;
	}

	$product_quantity              = isset( $products_quantity[ $product->get_id() ] ) ? ' x ' . $products_quantity[ $product->get_id() ] : '';
	$product_names_with_quantity[] = esc_html__( BWFAN_Common::get_name( $product ) . $product_quantity );
}

$explode_operator = apply_filters( 'bwfan_product_name_separator', ', ', 2 );
echo implode( $explode_operator, $product_names_with_quantity ); //phpcs:ignore WordPress.Security.EscapeOutput
