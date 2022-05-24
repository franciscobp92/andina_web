<?php

class BWFAN_unsubscribe {

	private static $ins = null;

	public function __construct() {

		/** Shortcodes for unsubscribe */
		add_shortcode( 'bwfan_unsubscribe_button', array( $this, 'bwfan_unsubscribe_button' ) );
		add_shortcode( 'wfan_unsubscribe_button', array( $this, 'bwfan_unsubscribe_button' ) );
		add_shortcode( 'bwfan_subscriber_recipient', array( $this, 'bwfan_subscriber_recipient' ) );
		add_shortcode( 'wfan_contact_email', array( $this, 'bwfan_subscriber_recipient' ) );
		add_shortcode( 'bwfan_subscriber_name', array( $this, 'bwfan_subscriber_name' ) );
		add_shortcode( 'wfan_contact_name', array( $this, 'bwfan_subscriber_name' ) );

		add_action( 'bwfan_db_1_0_tables_created', array( $this, 'create_unsubscribe_sample_page' ) );

		add_action( 'wp_head', array( $this, 'unsubscribe_page_non_crawlable' ) );

		/** Ajax Calls */
		add_action( 'wp_ajax_bwfan_select_unsubscribe_page', array( $this, 'bwfan_select_unsubscribe_page' ) );
		add_action( 'wp_ajax_bwfan_unsubscribe_user', array( $this, 'bwfan_unsubscribe_user' ) );
		add_action( 'wp_ajax_nopriv_bwfan_unsubscribe_user', array( $this, 'bwfan_unsubscribe_user' ) );
	}

	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self();
		}

		return self::$ins;
	}

	public function bwfan_unsubscribe_button( $attrs ) {
		$attr = shortcode_atts( array(
			'label' => __( 'Update my preference', 'wp-marketing-automations' ),
		), $attrs );

		ob_start();
		echo "<style type='text/css'>
			a#bwfan_unsubscribe {
			    text-shadow: none;
			    display: inline-block;
			    padding: 15px 20px;
			    cursor: pointer;
			    text-decoration: none !important;
			}
		</style>";

		echo '<div>';
		$this->print_unsubscribe_lists();

		echo '<a id="bwfan_unsubscribe" class="button-primary button" href="#">' . esc_html__( $attr['label'] ) . '</a>';
		if ( isset( $_GET['automation_id'] ) ) { // WordPress.CSRF.NonceVerification.NoNonceVerification
			echo '<input type="hidden" id="bwfan_automation_id" value="' . esc_attr__( sanitize_text_field( $_GET['automation_id'] ) ) . '">'; // WordPress.CSRF.NonceVerification.NoNonceVerification
		}

		if ( isset( $_GET['bid'] ) ) { // WordPress.CSRF.NonceVerification.NoNonceVerification
			echo '<input type="hidden" id="bwfan_broadcast_id" value="' . esc_attr__( sanitize_text_field( $_GET['bid'] ) ) . '">'; // WordPress.CSRF.NonceVerification.NoNonceVerification
		}

		if ( isset( $_GET['fid'] ) ) { // WordPress.CSRF.NonceVerification.NoNonceVerification
			echo '<input type="hidden" id="bwfan_form_feed_id" value="' . esc_attr__( sanitize_text_field( $_GET['fid'] ) ) . '">'; // WordPress.CSRF.NonceVerification.NoNonceVerification
		}

		if ( isset( $_GET['uid'] ) ) { // WordPress.CSRF.NonceVerification.NoNonceVerification
			echo '<input type="hidden" id="bwfan_form_uid_id" value="' . esc_attr__( sanitize_text_field( $_GET['uid'] ) ) . '">'; // WordPress.CSRF.NonceVerification.NoNonceVerification
		}

		echo '<input type="hidden" id="bwfan_unsubscribe_nonce" value="' . esc_attr( wp_create_nonce( 'bwfan-unsubscribe-nonce' ) ) . '" name="bwfan_unsubscribe_nonce">';
		echo '</div>';

		return ob_get_clean();
	}

	public function print_unsubscribe_lists() {
		/** If admin screen, return */
		if ( is_admin() ) {
			return false;
		}

		if ( ! bwfan_is_autonami_pro_active() ) {
			$this->only_unsubscribe_from_all_lists_html();

			return false;
		}

		$settings = BWFAN_Common::get_global_settings();
		$enabled  = isset( $settings['bwfan_unsubscribe_lists_enable'] ) ? $settings['bwfan_unsubscribe_lists_enable'] : 0;
		if ( 0 === absint( $enabled ) ) {
			$this->only_unsubscribe_from_all_lists_html();

			return false;
		}

		$lists = isset( $settings['bwfan_unsubscribe_public_lists'] ) ? $settings['bwfan_unsubscribe_public_lists'] : [];
		if ( empty( $lists ) || ! is_array( $lists ) ) {
			$this->only_unsubscribe_from_all_lists_html();

			return false;
		}

		$contact = ( isset( $_GET['uid'] ) && ! empty( $_GET['uid'] ) ) ? new WooFunnels_Contact( '', '', '', '', $_GET['uid'] ) : false;
		if ( ! $contact instanceof WooFunnels_Contact || 0 === $contact->get_id() ) {
			$this->only_unsubscribe_from_all_lists_html();

			return false;
		}

		$lists            = array_map( 'absint', $lists );
		$is_unsubscribed  = false;
		$contact          = new BWFCRM_Contact( $contact );
		$subscribed_lists = array();

		if ( $contact instanceof BWFCRM_Contact && $contact->is_contact_exists() ) {
			/** Is Unsubscribed Flag */
			$is_unsubscribed = BWFCRM_Contact::$DISPLAY_STATUS_UNSUBSCRIBED === $contact->get_display_status();

			$contact_lists    = $this->get_contact_lists( $contact );
			$subscribed_lists = $contact_lists['subscribed'];
			$contact_lists    = $contact_lists['contact_lists'];

			/** Show contact their subscribed public lists only */
			$visibility = isset( $settings['bwfan_unsubscribe_lists_visibility'] ) ? $settings['bwfan_unsubscribe_lists_visibility'] : 0;
			if ( 1 === absint( $visibility ) ) {
				/** Common lists from public lists and contact lists */
				$lists = array_values( array_intersect( $contact_lists, $lists ) );
			}

			if ( empty( $lists ) ) {
				$this->only_unsubscribe_from_all_lists_html( $contact );

				return false;
			}
		}

		$lists = BWFCRM_Lists::get_lists( $lists );

		usort( $lists, function ( $l1, $l2 ) {
			return strcmp( strtolower( $l1['name'] ), strtolower( $l2['name'] ) );
		} );
		$this->unsubscribe_lists_html( $lists, $subscribed_lists, $is_unsubscribed );

		return true;
	}

	public function only_unsubscribe_from_all_lists_html( $contact = false ) {
		if ( false === $contact ) {
			$contact = ( isset( $_GET['uid'] ) && ! empty( $_GET['uid'] ) ) ? new WooFunnels_Contact( '', '', '', '', $_GET['uid'] ) : false; // WordPress.CSRF.NonceVerification.NoNonceVerification
		}

		/** In case Pro is active and Contact is valid */
		if ( bwfan_is_autonami_pro_active() && class_exists( 'BWFCRM_Contact' ) && false !== $contact && $contact->get_id() > 0 ) {
			if ( $contact instanceof WooFunnels_Contact ) {
				$contact = new BWFCRM_Contact( $contact );
			}
			$is_unsubscribed = ( BWFCRM_Contact::$DISPLAY_STATUS_UNSUBSCRIBED === $contact->get_display_status() );

			$this->unsubscribe_lists_html( array(), array(), $is_unsubscribed );

			return;
		}

		/** If Pro is not active OR Contact is not valid */
		$recipient = isset( $_GET['subscriber_recipient'] ) ? $_GET['subscriber_recipient'] : '';
		if ( false !== $contact ) {
			$recipient = ( $contact->get_id() > 0 ) ? $contact->get_email() : $recipient;
		}

		$is_unsubscribed = false;
		if ( ! empty( $recipient ) ) {
			$is_unsubscribed = BWFAN_Model_Message_Unsubscribe::get_message_unsubscribe_row( array(
				'recipient' => array( $recipient ),
			), true );
			$is_unsubscribed = is_array( $is_unsubscribed ) && count( $is_unsubscribed ) > 0;
		}
		$this->unsubscribe_lists_html( array(), array(), $is_unsubscribed );
	}

	public function unsubscribe_lists_html( $lists = array(), $subscribed_lists = array(), $is_unsubscribed = false ) {
		$settings    = BWFAN_Common::get_global_settings();
		$label       = isset( $settings['bwfan_unsubscribe_from_all_label'] ) && ! empty( $settings['bwfan_unsubscribe_from_all_label'] ) ? $settings['bwfan_unsubscribe_from_all_label'] : __( '"Unsubscribe From All" Label', 'wp-marketing-automations' );
		$description = isset( $settings['bwfan_unsubscribe_from_all_description'] ) ? $settings['bwfan_unsubscribe_from_all_description'] : '';

		?>
        <style>
            .bwfan-unsubscribe-single-list {
                border-bottom: 1px solid #aaa;
                padding: 20px;
            }

            .bwfan-unsubscribe-single-list:last-child {
                border: none;
                padding: 20px;
            }

            .bwfan-unsubscribe-single-list p {
                margin-top: 3px;
                margin-bottom: 0;
            }

            .bwfan-unsubscribe-single-list label {
                margin-left: 10px;
            }

            p.bwfan-unsubscribe-list-description {
                font-size: 14px;
            }

            .bwfan-unsubscribe-lists {
                margin-bottom: 30px;
            }

            .bwfan-unsubscribe-from-all-lists label {
                font-size: 16px;
                font-weight: 500;
            }
        </style>
        <div class="bwfan-unsubscribe-lists" id="bwfan-unsubscribe-lists">
			<?php
			foreach ( $lists as $list ) {
				$is_checked = in_array( absint( $list['ID'] ), $subscribed_lists ) && ! $is_unsubscribed;
				?>
                <div class="bwfan-unsubscribe-single-list">
                    <div class="bwfan-unsubscribe-list-checkbox">
                        <input
                            id="bwfan-list-<?php echo $list['ID']; ?>"
                            type="checkbox"
                            value="<?php echo $list['ID']; ?>"
							<?php echo $is_checked ? 'checked="checked"' : ''; ?>
                        />
                        <label for="bwfan-list-<?php echo $list['ID']; ?>"><?php echo $list['name']; ?></label>
                    </div>
					<?php if ( isset( $list['description'] ) ) : ?>
                        <p class="bwfan-unsubscribe-list-description"><?php echo $list['description']; ?></p>
					<?php endif; ?>
                </div>
				<?php
			}
			?>
            <!-- Global Unsubscription option -->
            <div class="bwfan-unsubscribe-single-list bwfan-unsubscribe-from-all-lists">
                <div class="bwfan-unsubscribe-list-checkbox">
                    <input id="bwfan-list-unsubscribe-all" type="checkbox" value="unsubscribe_all" <?php echo $is_unsubscribed ? 'checked="checked"' : ''; ?> />
                    <label for="bwfan-list-unsubscribe-all"><?php echo $label; ?></label>
                </div>
				<?php if ( ! empty( $description ) ) : ?>
                    <p class="bwfan-unsubscribe-list-description"><?php echo $description; ?></p>
				<?php endif; ?>
            </div>
        </div>
		<?php
	}

	public function bwfan_subscriber_recipient( $attrs ) {
		$attr = shortcode_atts( array(
			'fallback' => 'john@example.com',
		), $attrs );

		$subscriber_details = $this->get_subscriber_details();

		$mode = 1;

		/** check if the mode 2 there then pass phone number instead of email */
		if ( isset( $_GET['mode'] ) && 2 === absint( $_GET['mode'] ) ) {
			$mode = 2;
		}

		if ( false !== $subscriber_details && 1 === absint( $mode ) && isset( $subscriber_details['subscriber_email'] ) ) { // WordPress.CSRF.NonceVerification.NoNonceVerification
			$attr['fallback'] = sanitize_text_field( $subscriber_details['subscriber_email'] ); // WordPress.CSRF.NonceVerification.NoNonceVerification
		} elseif ( false !== $subscriber_details && isset( $subscriber_details['subscriber_phone'] ) ) {
			$attr['fallback'] = sanitize_text_field( $subscriber_details['subscriber_phone'] );
		}

		return '<span id="bwfan_unsubscribe_recipient">' . $attr['fallback'] . '</span>';
	}

	/**
	 * Adding noindex, nofollow meta tag for unsubscribe page
	 */
	public function unsubscribe_page_non_crawlable() {
		$global_settings     = get_option( 'bwfan_global_settings' );
		$unsubscribe_page_id = isset( $global_settings['bwfan_unsubscribe_page'] ) ? $global_settings['bwfan_unsubscribe_page'] : 0;
		if ( ! empty( $unsubscribe_page_id ) && is_page( $unsubscribe_page_id ) ) {
			echo "\n<meta name='robots' content='noindex,nofollow' />\n";
		}
	}

	public function bwfan_subscriber_name( $attrs ) {
		$attr = shortcode_atts( array(
			'fallback' => 'John',
		), $attrs );

		$subscriber_details = $this->get_subscriber_details();
		if ( false !== $subscriber_details && isset( $subscriber_details['subscriber_name'] ) ) { // WordPress.CSRF.NonceVerification.NoNonceVerification
			$attr['fallback'] = sanitize_text_field( $subscriber_details['subscriber_name'] ); // WordPress.CSRF.NonceVerification.NoNonceVerification
		}

		return '<span id="bwfan_unsubscribe_name">' . $attr['fallback'] . '</span>';
	}

	public function create_unsubscribe_sample_page() {
		$global_settings = get_option( 'bwfan_global_settings', array() );
		$content         = "Hi [wfan_contact_name]\n\nHelp us to improve your experience with us through better communication. Please adjust your preferences for email [wfan_contact_email].\n\n[wfan_unsubscribe_button label='Update my preference']";

		$new_page = array(
			'post_title'   => __( 'Let\'s Keep In Touch', 'wp-marketing-automations' ),
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'page',
		);

		$post_id                                   = wp_insert_post( $new_page );
		$global_settings['bwfan_unsubscribe_page'] = $post_id;
		update_option( 'bwfan_global_settings', $global_settings );
	}

	public function bwfan_select_unsubscribe_page() {
		global $wpdb;
		$term    = isset( $_POST['search_term']['term'] ) ? sanitize_text_field( $_POST['search_term']['term'] ) : ''; // WordPress.CSRF.NonceVerification.NoNonceVerification
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT ID,post_title FROM {$wpdb->prefix}posts WHERE post_title LIKE %s and post_type = %s and post_status =%s", '%' . $term . '%', 'page', 'publish' ) );

		$response = array();
		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$response[] = array(
					'id'    => $result->ID,
					'text'  => $result->post_title,
					'value' => $result->ID,
					'label' => $result->post_title,
				);
			}
		}

		wp_send_json( array(
			'results' => $response,
		) );
	}

	public function bwfan_unsubscribe_user() {
		global $wpdb;
		$nonce = ( isset( $_POST['_nonce'] ) ) ? sanitize_text_field( $_POST['_nonce'] ) : ''; //phpcs:ignore WordPress.Security.NonceVerification
		if ( ! wp_verify_nonce( $nonce, 'bwfan-unsubscribe-nonce' ) ) {
			return;
		}

		if ( ! isset( $_POST['recipient'] ) || ( ! isset( $_POST['automation_id'] ) && ! isset( $_POST['broadcast_id'] ) ) ) { // WordPress.CSRF.NonceVerification.NoNonceVerification
			wp_send_json( array(
				'success' => 0,
				'message' => __( 'Security check failed', 'wp-marketing-automations' ),
			) );
		}

		$global_settings = BWFAN_Common::get_global_settings();
		$recipient       = sanitize_text_field( $_POST['recipient'] ); // WordPress.CSRF.NonceVerification.NoNonceVerification
		$uid             = sanitize_text_field( $_POST['uid'] ); // WordPress.CSRF.NonceVerification.NoNonceVerification

		/** still check for recipient details in case other details are available */
		if ( empty( $recipient ) || empty( $uid ) ) {
			wp_send_json( array(
				'success' => 0,
				'message' => __( 'Unable to unsubscribe. No contact found.', 'wp-marketing-automations' ),
			) );
		}

		$automation_id = absint( sanitize_text_field( $_POST['automation_id'] ) ); // WordPress.CSRF.NonceVerification.NoNonceVerification
		$broadcast_id  = absint( sanitize_text_field( $_POST['broadcast_id'] ) ); // WordPress.CSRF.NonceVerification.NoNonceVerification
		$form_feed_id  = absint( sanitize_text_field( $_POST['form_feed_id'] ) ); // WordPress.CSRF.NonceVerification.NoNonceVerification

		if ( false !== filter_var( $recipient, FILTER_VALIDATE_EMAIL ) ) {
			$mode = 1;
		} elseif ( is_numeric( $recipient ) ) {
			$mode = 2;
		} else {
			wp_send_json( array(
				'success' => 0,
				'message' => __( 'Unable to unsubscribe. No contact found.', 'wp-marketing-automations' ),
			) );
		}

		$this->handle_unsubscribe_lists_submission();

		/**
		 * Checking if recipient already added to unsubscribe table
		 */
		$where         = "WHERE `recipient` = '" . sanitize_text_field( $recipient ) . "' and `mode` = '" . $mode . "'";
		$unsubscribers = $wpdb->get_var( "SELECT ID FROM {$wpdb->prefix}bwfan_message_unsubscribe $where ORDER BY ID DESC LIMIT 0,1 " );//phpcs:ignore WordPress.DB.PreparedSQL

		if ( $unsubscribers > 0 ) {
			wp_send_json( array(
				'success' => 0,
				'message' => __( 'You have already unsubscribed', 'wp-marketing-automations' ),
			) );
		}

		/** Manual (Single Sending) */
		$c_type = 3;
		if ( ! empty( $automation_id ) ) {
			$c_type = 1;
		} elseif ( ! empty( $broadcast_id ) ) {
			$c_type = 2;
		} elseif ( ! empty( $form_feed_id ) ) {
			$c_type = 4;
		}

		$oid = 0;
		if ( ! empty( $automation_id ) ) {
			$oid = absint( $automation_id );
		} elseif ( ! empty( $broadcast_id ) ) {
			$oid = absint( $broadcast_id );
		} elseif ( ! empty( $form_feed_id ) ) {
			$oid = absint( $form_feed_id );
		}

		$insert_data = array(
			'recipient'     => $recipient,
			'c_date'        => current_time( 'mysql' ),
			'mode'          => $mode,
			'automation_id' => $oid,
			'c_type'        => $c_type,
		);

		BWFAN_Model_Message_Unsubscribe::insert( $insert_data );
		/** hook when any contact unsubscribed  */
		do_action( 'bwfcrm_after_contact_unsubscribed', array( $insert_data ) );

		wp_send_json( array(
			'success' => 1,
			'message' => $global_settings['bwfan_unsubscribe_data_success'],
		) );
	}

	public function handle_unsubscribe_lists_submission() {
		/** If invalid lists data */
		if ( ! isset( $_POST['unsubscribe_lists'] ) || empty( $_POST['unsubscribe_lists'] ) ) {
			wp_send_json( array(
				'success' => 0,
				'message' => __( 'Invalid Unsubscribe Data', 'wp-marketing-automations' ),
			) );
		}

		/** If unsubscribe from all then do normal unsubscribe operation */
		if ( false !== strpos( $_POST['unsubscribe_lists'], 'all' ) ) {
			return;
		}

		$lists = json_decode( $_POST['unsubscribe_lists'], true );
		if ( empty( $lists ) ) {
			$lists = array();
		}

		$lists = array_map( 'sanitize_text_field', $lists );
		$lists = array_map( 'strval', $lists );

		/** If unsubscribe_lists doesn't contains 'all',
		 * and lists are also empty, then resubscribe only, if pro is not active.
		 * (Because there will be no lists view if pro not active,
		 * and submission means 'User has unchecked the "Unsubscribe from all"')
		 * */
		if ( empty( $lists ) && ! bwfan_is_autonami_pro_active() ) {
			$this->maybe_resubscribe();

			return;
		}

		$contact = BWFCRM_Common::get_contact_by_email_or_phone( sanitize_text_field( $_POST['recipient'] ) );
		if ( ! $contact instanceof BWFCRM_Contact ) {
			wp_send_json( array(
				'success' => 0,
				'message' => __( 'Invalid Recipient', 'wp-marketing-automations' ),
			) );
		}

		/** If 'all' is missing, then do subscribe the contact */
		if ( BWFCRM_Contact::$DISPLAY_STATUS_UNSUBSCRIBED === $contact->get_display_status() ) {
			$contact->resubscribe();
		}

		/** Get Public Lists */
		$settings     = BWFAN_Common::get_global_settings();
		$public_lists = ! empty( $settings['bwfan_unsubscribe_public_lists'] ) ? $settings['bwfan_unsubscribe_public_lists'] : [];

		/** Get Contact List */
		$contact_lists = $this->get_contact_lists( $contact );
		$contact_lists = $contact_lists['contact_lists'];

		$visibility = $settings['bwfan_unsubscribe_lists_visibility'];
		if ( 1 === absint( $visibility ) ) {
			/** Public Lists for Contact will consists of only which contact has been added to */
			$public_lists = array_values( array_intersect( $contact_lists, $public_lists ) );
		}

		$lists = array_map( 'sanitize_text_field', $lists );
		$lists = array_map( 'strval', $lists );

		$lists_to_add   = array_values( array_diff( $public_lists, $contact_lists, $lists ) );
		$lists_to_unsub = array_values( array_intersect( $public_lists, $contact_lists, $lists ) );

		/** Subscribe to lists which are checked, but not assigned to contact */
		$contact_lists = array_merge( $contact_lists, $lists_to_add );
		$contact_lists = array_diff( $contact_lists, $lists_to_unsub );
		$contact->contact->set_lists( $contact_lists );

		/** Unsubscribe from lists which are unchecked, but are assigned to contact */
		$contact->set_field_by_slug( 'unsubscribed-lists', wp_json_encode( $lists_to_unsub ) );
		$contact->save_fields();

		$contact->contact->set_last_modified( current_time( 'mysql', 1 ) );
		if ( method_exists( $contact, 'save' ) ) {
			$contact->save();
		} else {
			$contact->contact->save();
		}

		wp_send_json( array(
			'success' => 1,
			'message' => __( 'Your Lists preferences are saved!', 'wp-marketing-automations' ),
		) );
	}

	/**
	 * @param BWFCRM_Contact $contact
	 *
	 * @return array
	 * */
	public function get_contact_lists( $contact ) {
		/** Get Unsubscribed Lists */
		$unsubscribed_lists = $contact->get_field_by_slug( 'unsubscribed-lists' );
		$unsubscribed_lists = empty( $unsubscribed_lists ) ? array() : json_decode( $unsubscribed_lists, true );
		$unsubscribed_lists = array_map( 'absint', $unsubscribed_lists );

		/** Get Contact Lists (Include Unsubscribed Lists) */
		$subscribed_lists = $contact->get_lists();
		$subscribed_lists = array_map( 'absint', $subscribed_lists );
		$contact_lists    = array_values( array_merge( $subscribed_lists, $unsubscribed_lists ) );

		return array(
			'subscribed'    => $subscribed_lists,
			'unsubscribed'  => $unsubscribed_lists,
			'contact_lists' => $contact_lists,
		);
	}

	public function maybe_resubscribe() {
		global $wpdb;

		$recipient = sanitize_text_field( $_POST['recipient'] ); // WordPress.CSRF.NonceVerification.NoNonceVerification
		if ( empty( $recipient ) ) {
			wp_send_json( array(
				'success' => 0,
				'message' => __( 'Empty Recipient', 'wp-marketing-automations' ),
			) );

			return;
		}

		$mode = 2;
		if ( false !== filter_var( $recipient, FILTER_VALIDATE_EMAIL ) ) {
			$mode = 1;
		} elseif ( is_numeric( $recipient ) ) {
			$mode = 2;
		} else {
			wp_send_json( array(
				'success' => 0,
				'message' => __( 'Invalid Recipient', 'wp-marketing-automations' ),
			) );
		}

		$where         = "WHERE `recipient` = '" . sanitize_text_field( $recipient ) . "' and `mode` = '" . $mode . "'";
		$unsubscribers = $wpdb->get_results( "SELECT ID,recipient FROM {$wpdb->prefix}bwfan_message_unsubscribe $where ORDER BY ID DESC", ARRAY_A );//phpcs:ignore WordPress.DB.PreparedSQL
		if ( ! empty( $unsubscribers ) ) {
			foreach ( $unsubscribers as $unsubscriber ) {
				$id        = $unsubscriber['ID'];
				$recipient = $unsubscriber['recipient'];
				$result    = BWFAN_Model_Message_Unsubscribe::delete( $id );
				//do_action( 'bwfan_unsubscribers_deleted', $result, $recipient );
			}

			wp_send_json( array(
				'success' => 1,
				'message' => __( 'You are now re-subscribed', 'wp-marketing-automations' ),
			) );
		}

		wp_send_json( array(
			'success' => 0,
			'message' => __( 'You are already subscribed', 'wp-marketing-automations' ),
		) );
	}

	/** get subscriber details using uid
	 * @return false
	 */
	public function get_subscriber_details( $uid = '' ) {

		$contact = '';
		if ( ! isset( $_GET['uid'] ) && isset( $_GET['subscriber_recipient'] ) ) {
			/** Get contact by email */
			$contact = new WooFunnels_Contact( '', $_GET['subscriber_recipient'] );
			/** Get contact by phone */
			$contact = ( 0 === absint( $contact->get_id() ) ) ? new WooFunnels_Contact( '', '', $_GET['subscriber_recipient'] ) : $contact;

			if ( 0 === absint( $contact->get_id() ) ) {
				return false;
			}

			$_GET['uid'] = $contact->get_uid();
		}

		if ( ! isset( $_GET['uid'] ) && empty( $contact ) ) {
			/** Get contact if logged in */
			$contact = $this->get_logged_in_contact();

			$_GET['uid'] = ( false !== $contact && absint( $contact->get_id() ) > 0 ) ? $contact->get_uid() : '';
		}

		$uid = isset( $_GET['uid'] ) ? $_GET['uid'] : $uid;

		if ( empty( $uid ) ) { // WordPress.CSRF.NonceVerification.NoNonceVerification
			return false;
		}

		$contact = empty( $contact ) ? new WooFunnels_Contact( '', '', '', '', $uid ) : $contact;// WordPress.CSRF.NonceVerification.NoNonceVerification
		if ( 0 === absint( $contact->get_id() ) ) {
			return false;
		}

		$contact_details['subscriber_email'] = $contact->get_email();
		$contact_details['subscriber_phone'] = $contact->get_contact_no();

		$contact_details['subscriber_name'] = ucwords( $contact->get_f_name() . ' ' . $contact->get_l_name() );

		return $contact_details;
	}

	public function get_logged_in_contact() {

		if ( ! is_user_logged_in() ) {
			return false;
		}

		return new WooFunnels_Contact( get_current_user_id() );

	}
}

BWFAN_unsubscribe::get_instance();
