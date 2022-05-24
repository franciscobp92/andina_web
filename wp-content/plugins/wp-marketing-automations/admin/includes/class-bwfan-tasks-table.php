<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BWFAN_Tasks_Table extends WP_List_Table {

	public static $per_page = 20;
	public static $current_page;
	public $data;
	public $meta_data;
	public $date_format;
	public $automation_id = null;
	public $include_automation_column = true;
	private $localize_data = [];

	/**meta_data
	 * Constructor.
	 * @since  1.0.0
	 */
	public function __construct( $args = array() ) {
		self::$current_page = $this->get_pagenum();
		$this->data         = array();
		$this->date_format  = BWFAN_Common::get_date_format();
		// Make sure this file is loaded, so we have access to plugins_api(), etc.
		require_once( ABSPATH . '/wp-admin/includes/plugin-install.php' );

		parent::__construct( $args );

		add_filter( 'removable_query_args', [ $this, 'remove_query_params' ], 10, 1 );
	}

	public static function render_trigger_nav() {
		$html = BWFAN_Common::get_link_options_for_tasks();
		echo wp_kses_post( $html );
	}

	/**
	 * Text to display if no items are present.
	 * @return  void
	 * @since  1.0.0
	 */
	public function no_items() {
		$status  = filter_input( INPUT_GET, 'status' );
		$message = wpautop( esc_html__( 'There are no scheduled tasks', 'wp-marketing-automations' ) );
		if ( 't_1' === $status ) {
			$message = wpautop( esc_html__( 'There are no paused tasks', 'wp-marketing-automations' ) );
		}

		echo wp_kses_post( $message );
	}

	/** Made the data for Tasks screen.
	 * @return array
	 */
	public function get_tasks_table_data() {
		global $wpdb;
		$rows = BWFAN_Core()->tasks->get_all_tasks();

		if ( ! is_array( $rows ) || count( $rows ) === 0 ) {
			return array();
		}

		$found_posts = array();
		$dependency  = array(
			'dependency_table' => $wpdb->prefix . 'bwfan_automations',
			'col_name'         => 'status',
			'col_value'        => '1',
			'dependency_col'   => 'ID',
			'dependent_col'    => 'automation_id',
		);

		// Fetch the tasks of only 1 automation
		if ( ! is_null( $this->automation_id ) ) {

			$dependency['automation_id']    = $this->automation_id;
			$dependency['automation_table'] = $wpdb->prefix . 'bwfan_automations';
			$dependency['automation_col']   = 'ID';
		}

		$found_posts['found_posts'] = BWFAN_Model_Tasks::count_rows( $dependency );

		// If tasks are filtered, then show the count of filtered data
		if ( BWFAN_Core()->tasks->show_filtered_tasks_count ) {
			$found_posts['found_posts'] = BWFAN_Core()->tasks->filtered_tasks_count;
		}

		$items = array();
		$gif   = admin_url() . 'images/wpspin_light.gif';

		BWFAN_Core()->automations->return_all = true;
		$active_automations                   = BWFAN_Core()->automations->get_all_automations();
		BWFAN_Core()->automations->return_all = false;

		foreach ( $rows as $task_id => $task ) {
			$automation_id = $task['automation_id'];
			if ( ! isset( $active_automations[ $automation_id ] ) ) {
				continue;
			}

			$source_slug      = isset( $task['meta']['integration_data']['event_data'] ) ? $task['meta']['integration_data']['event_data']['event_source'] : null;
			$event_slug       = isset( $task['meta']['integration_data']['event_data'] ) ? $task['meta']['integration_data']['event_data']['event_slug'] : null;
			$integration_slug = $task['integration_slug'];

			// Event plugin is deactivated, so don't show the automations
			$source_instance = BWFAN_Core()->sources->get_source( $source_slug );

			/**
			 * @var $event_instance BWFAN_Event
			 */
			$event_instance = BWFAN_Core()->sources->get_event( $event_slug );

			$task_details   = isset( $task['meta']['integration_data']['global'] ) ? $task['meta']['integration_data']['global'] : array();
			$message        = ( isset( $task['meta']['task_message'] ) ) ? BWFAN_Common::get_parsed_time( $this->date_format, maybe_unserialize( $task['meta']['task_message'] ) ) : array();
			$status         = $task['status'];
			$automation_url = add_query_arg( array(
				'page'    => 'autonami-automations',
				'edit'    => $automation_id,
			), admin_url( 'admin.php' ) );

			$action_slug       = $task['integration_action'];
			$items[ $task_id ] = array(
				'id'                      => $task_id,
				'automation_id'           => $automation_id,
				'automation_name'         => $task['title'],
				'automation_url'          => $automation_url,
				'automation_source'       => ! is_null( $source_instance ) ? $source_instance->get_name() : __( 'Data unavailable. Contact Support.', 'wp-marketing-automations' ),
				'automation_event'        => ! is_null( $event_instance ) ? $event_instance->get_name() : __( 'Data unavailable. Contact Support.', 'wp-marketing-automations' ),
				'task_integration'        => esc_html__( 'Not Found', 'wp-marketing-automations' ),
				'task_integration_action' => esc_html__( 'Not Found', 'wp-marketing-automations' ),
				'task_date'               => BWFAN_Common::get_human_readable_time( $task['e_date'], get_date_from_gmt( date( 'Y-m-d H:i:s', $task['e_date'] ), $this->date_format ) ),
				'status'                  => $status,
				'gif'                     => $gif,
				'task_message'            => $message,
				'task_details'            => '',
				'task_corrupted'          => false
			);
			/**
			 * @var $action_instance BWFAN_Action
			 */
			$action_instance = BWFAN_Core()->integration->get_action( $action_slug );
			if ( ! is_null( $action_instance ) ) {
				$items[ $task_id ]['task_integration_action'] = $action_instance->get_name();
			} else {
				$action_name = BWFAN_Common::get_entity_nice_name( 'action', $action_slug );
				if ( ! empty( $action_name ) ) {
					$items[ $task_id ]['task_integration_action'] = $action_name;
				}
			}

			/**
			 * @var $event_instance BWFAN_Event
			 */
			$integration_instance = BWFAN_Core()->integration->get_integration( $integration_slug );
			if ( ! is_null( $integration_instance ) ) {
				$items[ $task_id ]['task_integration'] = $integration_instance->get_name();
				$task_details['task_integration']      = $integration_instance->get_name();
			} else {
				$integration_name = BWFAN_Common::get_entity_nice_name( 'integration', $integration_slug );
				if ( ! empty( $integration_name ) ) {
					$items[ $task_id ]['task_integration'] = $integration_name;
					$task_details['task_integration']      = $integration_name;
				}
			}
			$items[ $task_id ]['task_details']   = ! is_null( $event_instance ) ? $event_instance->get_task_view( $task_details ) : '<b>' . __( 'Data unavailable. Contact Support.', 'wp-marketing-automations' ) . '</b>';
			$items[ $task_id ]['task_corrupted'] = is_null( $event_instance ) || is_null( $source_instance );
			$this->localize_data[ $task_id ]     = $items[ $task_id ];
		}

		$found_posts['items'] = $items;

		return $found_posts;
	}

	/**
	 * The content of each column.
	 *
	 * @param array $item The current item in the list.
	 * @param string $column_name The key of the current column.
	 *
	 * @return string              Output for the current column.
	 * @since  1.0.0
	 */
	public function column_default( $item, $column_name ) {
		$status = '';
		switch ( $column_name ) {
			case 'check-column':
				$status = '&nbsp;';
				break;
			case 'status':
				$status = $item[ $column_name ];
				break;
		}

		return $status;
	}

	public function column_task( $item ) {
		$column_string = '#' . $item['id'];

		return $column_string;
	}

	public function column_details( $item ) {
		return '<div class="bwfan-extra-details">' . $item['task_details'] . '</div>';
	}

	public function column_automation( $item ) {
		$column_string = '<a href="' . $item['automation_url'] . '" class="row-title">' . $item['automation_name'] . ' (#' . $item['automation_id'] . ')</a>';

		return $column_string;
	}

	public function column_action( $item ) {
		$column_string = sprintf( '<a href="javascript:void(0);" class="bwfan-preview" data-task-id="%d" title="' . esc_html__( 'Preview', 'wp-marketing-automations' ) . '" data-izimodal-open="#modal-show-task-details" data-iziModal-title="Task Details" data-izimodal-transitionin="comingIn">%s</a>', $item['id'], esc_html__( 'Preview', 'wp-marketing-automations' ) );

		return apply_filters( 'bwfan_task_list_col_action', $column_string . $item['task_integration'] . ': ' . $item['task_integration_action'], $item );
	}

	public function column_time( $item ) {
		return $item['task_date'];
	}

	public function column_execute( $item ) {
		$column_string = '';
		if ( 'Not Found' !== $item['task_integration'] && '1' !== $item['status'] && false === $item['task_corrupted'] ) {
			$column_string = sprintf( '<a href="javascript:void(0);" class="bwfan-run-task" data-task-id="%d" title="' . esc_html__( 'Run Now', 'wp-marketing-automations' ) . '">%s</a>', $item['id'], esc_html__( 'Run Now', 'wp-marketing-automations' ) );
			$column_string .= ' | ';
		}
		$column_string .= sprintf( '<a href="javascript:void(0);" class="bwfan-delete-task" data-task-id="%d" title="' . esc_html__( 'Delete', 'wp-marketing-automations' ) . '">%s</a>', $item['id'], esc_html__( 'Delete', 'wp-marketing-automations' ) );
		$column_string .= '<span class="bwfan-gif-task-delete bwfan-display-none"><img src=" ' . $item['gif'] . '"></span>';

		return $column_string;
	}

	/**
	 * Prepare an array of items to be listed.
	 * @since  1.0.0
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$total_items           = ( isset( $this->data['found_posts'] ) ) ? $this->data['found_posts'] : 0;

		$this->set_pagination_args( array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => self::$per_page, //WE have to determine how many items to show on a page
		) );
		$this->items = ( isset( $this->data['items'] ) ) ? $this->data['items'] : array();
	}

	/**
	 * Retrieve an array of columns for the list table.
	 * @return array Key => Value pairs.
	 * @since  1.0.0
	 */
	public function get_columns() {
		$columns = array(
			'cb'      => '<input type="checkbox" />',
			'task'    => esc_html__( 'Task', 'wp-marketing-automations' ),
			'action'  => esc_html__( 'Action', 'wp-marketing-automations' ),
			'details' => esc_html__( 'Data', 'wp-marketing-automations' ),
		);

		if ( $this->include_automation_column ) {
			$columns['automation'] = esc_html__( 'Automation', 'wp-marketing-automations' );
		}

		$columns['time']    = esc_html__( 'Date', 'wp-marketing-automations' );
		$columns['execute'] = '';

		return $columns;
	}

	/**
	 * Process all the records for the bulk action taken by user.
	 */
	public function process_bulk_action() {
		if ( ( isset( $_GET['action'] ) && '' !== sanitize_text_field( $_GET['action'] ) ) || ( isset( $_GET['action2'] ) && '' !== sanitize_text_field( $_GET['action2'] ) ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification
			$action = ( ! empty( $_GET['action'] ) ) ? sanitize_text_field( $_GET['action'] ) : sanitize_text_field( $_GET['action2'] ); // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification

			switch ( $action ) {
				case 'reschedule':
					if ( isset( $_GET['bwfan_task_ids'] ) && is_array( $_GET['bwfan_task_ids'] ) && count( $_GET['bwfan_task_ids'] ) > 0 ) { // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification, WordPress.Security.ValidatedSanitizedInput
						$task_status = ( isset( $_GET['status'] ) && '' !== sanitize_text_field( $_GET['status'] ) ) ? sanitize_text_field( $_GET['status'] ) : 't_0'; // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification
						$task_status = explode( '_', $task_status );
						$task_status = intval( $task_status[1] );

						if ( 0 === $task_status ) {
							BWFAN_Core()->tasks->rescheduled_tasks( true, $_GET['bwfan_task_ids'] ); // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification, WordPress.Security.ValidatedSanitizedInput
							echo '<span class="bwfan_show_toastr" data-msg="' . esc_html__( 'Selected Tasks scheduled to run now', 'wp-marketing-automations' ) . '"></span>';
						} else {
							echo '<span class="bwfan_show_toastr" data-msg="' . esc_html__( 'Paused Tasks cannot be executed', 'wp-marketing-automations' ) . '"></span>';
						}
					}
					break;
				case 'delete':
					if ( isset( $_GET['bwfan_task_ids'] ) && is_array( $_GET['bwfan_task_ids'] ) && count( $_GET['bwfan_task_ids'] ) > 0 ) { // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification, WordPress.Security.ValidatedSanitizedInput
						BWFAN_Core()->tasks->delete_tasks( $_GET['bwfan_task_ids'] ); // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification, WordPress.Security.ValidatedSanitizedInput
						echo '<span class="bwfan_show_toastr" data-msg="' . esc_html__( 'Selected Tasks Deleted', 'wp-marketing-automations' ) . '"></span>';
					}
					break;
				default:
					break;
			}
		}
	}

	public function display() {
		$singular = $this->_args['singular'];
		$this->display_tablenav( 'top' );
		$this->screen->render_screen_reader_content( 'heading_list' );

		?>
        <table class="wp-list-table <?php echo esc_attr( implode( ' ', $this->get_table_classes() ) ); ?>">
            <thead>
            <tr>
				<?php $this->print_column_headers(); ?>
            </tr>
            </thead>
            <tbody id="the-list"
				<?php
				if ( $singular ) {
					echo esc_html( " data-wp-lists='list:$singular'" );
				}
				?>
            >
			<?php $this->display_rows_or_placeholder(); ?>
            </tbody>
            <tfoot>
            <tr>
				<?php $this->print_column_headers( false ); ?>
            </tr>
            </tfoot>
        </table>
		<?php
		$this->display_tablenav( 'bottom' );
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @param string $which
	 *
	 * @since 3.1.0
	 *
	 */
	protected function display_tablenav( $which ) {
		?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">
			<?php
			$this->extra_tablenav( $which );
			$this->bulk_actions( 'top' );
			$this->pagination( 'bottom' );
			?>
            <br class="clear"/>
        </div>
		<?php
	}

	public function bulk_actions( $which = '' ) {
		if ( is_null( $this->_actions ) ) {
			$this->_actions = $this->get_bulk_actions();
			/**
			 * Filters the list table Bulk Actions drop-down.
			 *
			 * The dynamic portion of the hook name, `$this->screen->id`, refers
			 * to the ID of the current screen, usually a string.
			 *
			 * This filter can currently only be used to remove bulk actions.
			 *
			 * @param array $actions An array of the available bulk actions.
			 *
			 * @since 3.5.0
			 *
			 */
			$this->_actions = apply_filters( "bulk_actions-{$this->screen->id}", $this->_actions );
			$two            = '';
		} else {
			$two = '2';
		}

		if ( empty( $this->_actions ) ) {
			return;
		}

		echo '<div class="bwfan_filter_section">';
		echo '<label for="bulk-action-selector-' . esc_attr( $which ) . '" class="screen-reader-text">' . esc_html__( 'Select bulk action', 'wp-marketing-automations' ) . '</label>';
		echo '<select name="action' . esc_attr( $two ) . '" id="bulk-action-selector-' . esc_attr( $which ) . "\">\n";
		echo '<option value="">' . esc_html__( 'Bulk Actions', 'wp-marketing-automations' ) . "</option>\n";

		foreach ( $this->_actions as $name => $title ) {
			$class = 'edit' === $name ? ' class="hide-if-no-js"' : '';

			echo "\t" . '<option value="' . esc_attr( $name ) . '"' . esc_attr( $class ) . '>' . esc_attr( $title ) . "</option>\n";
		}

		echo "</select>\n";

		submit_button( esc_html__( 'Apply', 'wp-marketing-automations' ), 'action', '', false, array(
			'id' => "doaction$two",
		) );
		echo "\n";
		echo '</div>';

		include_once( BWFAN_PLUGIN_DIR . '/admin/view/tasks-page-filter.php' );
	}

	/**
	 * Retrieve an array of possible bulk actions.
	 * @return array
	 * @since  1.0.0
	 */
	public function get_bulk_actions() {
		$actions = array(
			'delete'     => esc_html__( 'Delete', 'wp-marketing-automations' ),
			'reschedule' => esc_html__( 'Execute', 'wp-marketing-automations' ),
		);

		return $actions;
	}

	public function get_table_classes() {
		$get_default_classes = parent::get_table_classes();
		array_push( $get_default_classes, 'bwfan-instance-table' );
		array_push( $get_default_classes, 'bwfan-list_tasks' );

		return $get_default_classes;
	}

	public function column_cb( $item ) {
		if ( false === $item['task_corrupted'] ) {
			?>
            <div class='bwfan_fsetting_table_title'>
                <div class=''>
                    <input name='bwfan_task_ids[]' data-id="<?php echo esc_attr( $item['id'] ); ?>" value="<?php echo esc_attr( $item['id'] ); ?>" type='checkbox'/>
                    <label for='' class=''></label>
                </div>
            </div>
			<?php
		}
	}

	public function single_row( $item ) {
		echo '<tr class="bwfan_automation list_tasks">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Displays the search box.
	 *
	 * @param string $text The 'submit' button label.
	 * @param string $input_id ID attribute value for the search input field.
	 *
	 * @since 3.1.0
	 *
	 */
	public function search_box( $text = '', $input_id = 'bwfan' ) {
		$input_id = $input_id . '-search-input';
		?>
        <p class="search-box">
            <label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $text ); ?>:</label>
            <input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>"/>
			<?php
			submit_button( $text, '', '', false, array(
				'id' => 'search-submit',
			) );
			?>
        </p>
		<?php
	}

	public function remove_query_params( $query_params ) {
		if ( BWFAN_Common::is_autonami_page() ) {
			$query_params[] = 'action';
			$query_params[] = 'action2';
			$query_params[] = 'ba';
			$query_params[] = 'bwfan_task_ids';
		}

		return $query_params;
	}

	public function print_local_data() {

		?>
        <script>
            var bwfan_task_table_local =<?php echo count( $this->localize_data ) > 0 ? wp_json_encode( $this->localize_data ) : '{}'; ?>;
        </script>
		<?php

	}

}
