<?php

/**
 * Clearfy Pro
 * https://wpshop.ru/plugins/clearfy
 */
class BWFAN_Compatibility_With_Clearfy {

	public function __construct() {
		add_filter( 'clearfy_rest_api_white_list', array( $this, 'bwfan_whitelist_autonami_endpoints' ), 10, 1 );
	}

	/** white list autonami endpoints in clearfy pro plugin
	 *
	 * @param $white_list
	 *
	 * @return mixed
	 */
	public function bwfan_whitelist_autonami_endpoints( $white_list ) {
		$white_list[] = 'woofunnels';
		$white_list[] = 'woofunnels-admin';
		$white_list[] = 'autonami-admin';
		$white_list[] = 'autonami-webhook';
		$white_list[] = 'woofunnels-analytics';
		$white_list[] = 'autonami';

		return $white_list;
	}
}

if ( class_exists( 'Clearfy_Plugin' ) ) {
	new BWFAN_Compatibility_With_Clearfy();
}
