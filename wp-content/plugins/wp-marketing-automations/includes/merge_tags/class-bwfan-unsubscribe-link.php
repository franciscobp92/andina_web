<?php

class BWFAN_Unsubscribe_Link extends BWFAN_Merge_Tag {

	private static $instance = null;

	public function __construct() {
		$this->tag_name        = 'unsubscribe_link';
		$this->tag_description = __( 'Unsubscribe URL', 'wp-marketing-automations' );
		add_shortcode( 'bwfan_unsubscribe_link', array( $this, 'parse_shortcode' ) );
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
		if ( true === BWFAN_Merge_Tag_Loader::get_data( 'is_preview' ) ) {
			return $this->get_dummy_preview();
		}

		$get_data   = BWFAN_Merge_Tag_Loader::get_data();
		$user_id    = isset( $get_data['user_id'] ) ? $get_data['user_id'] : '';
		$email      = isset( $get_data['email'] ) ? $get_data['email'] : '';
		$contact_id = isset( $get_data['contact_id'] ) ? $get_data['contact_id'] : '';

		$unsubscribe_link = $this->get_unsubscribe_page_url();

		$uid = '';

		/** If Contact ID */
		if ( bwfan_is_autonami_pro_active() ) {
			$uid = $this->get_uid( $contact_id );
		}

		/** if user id || email */
		if ( empty( $uid ) ) {
			$contact = bwf_get_contact( $user_id, $email );
			if ( $contact->get_id() > 0 ) {
				$uid = $contact->get_uid();
			}
		}

		if ( ! empty( $uid ) ) {
			$unsubscribe_link = add_query_arg( array(
				'uid' => $uid,
			), $unsubscribe_link );
		}

		$unsubscribe_link = apply_filters( 'bwfan_unsubscribe_link', $unsubscribe_link, $attr );

		return $this->parse_shortcode_output( $unsubscribe_link, $attr );
	}

	/** get contact uid using contact id
	 *
	 * @param $cid
	 *
	 * @return false|string
	 *
	 */
	public function get_uid( $cid ) {
		$cid = absint( $cid );
		if ( 0 === $cid ) {
			return 0;
		}
		$contact = new WooFunnels_Contact( '', '', '', $cid );
		if ( $contact->get_id() > 0 ) {
			return $contact->get_uid();
		}

		return 0;
	}

	/** get the unsubscribe page url
	 * @return string
	 */
	public function get_unsubscribe_page_url() {
		$global_settings = BWFAN_Common::get_global_settings();
		if ( ! isset( $global_settings['bwfan_unsubscribe_page'] ) || empty( $global_settings['bwfan_unsubscribe_page'] ) ) {
			return '';
		}

		$page      = absint( $global_settings['bwfan_unsubscribe_page'] );
		$page_link = get_permalink( $page );

		$unsubscribe_link = add_query_arg( array(
			'bwfan-action' => 'unsubscribe',
		), $page_link );

		return $unsubscribe_link;
	}

	/**
	 * Show dummy value of the current merge tag.
	 *
	 * @return string
	 */
	public function get_dummy_preview() {
		$email             = BWFAN_Merge_Tag_Loader::get_data( 'test_email' );
		$email             = ! empty( $email ) ? $email : 'johndoe@gmail.com';
		$unsubscribe_link  = $this->get_unsubscribe_page_url();
		$unsubscriber_name = 'John';

		/** get the current user details */
		$user = wp_get_current_user();
		if ( $user instanceof WP_User && $user->ID > 0 ) {
			$email             = $user->user_email;
			$unsubscriber_name = ! empty( $user->first_name ) ? $user->first_name : $unsubscriber_name;
		}

		$unsubscribe_link = add_query_arg( array(
			'subscriber_recipient' => $email,
			'subscriber_name'      => $unsubscriber_name,
		), $unsubscribe_link );

		return $unsubscribe_link;
	}


}

/**
 * Register this merge tag to a group.
 */
BWFAN_Merge_Tag_Loader::register( 'bwfan_default', 'BWFAN_Unsubscribe_Link' );
