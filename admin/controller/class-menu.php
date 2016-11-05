<?php
/**
 * Admin menu
 *
 * @since 2.0
 **/
class CoursePress_Admin_Controller_Menu {
	var $parent_slug 				= '';
	var $slug 						= '';
	protected $cap 					= 'manage_options'; // Default to admin cap
	var $description 				= '';
	var $with_editor 				= false;
	protected static $labels;
	protected static $post_type;

	/**
	 * @var (bool)		A helper var to identify if current page is the page set for this menu.
	 **/
	var $is_page_loaded 			= false;

	var $scripts 					= array();
	var $css 						= array();
	/** @var (associative_array)	Use as container for localize text/settings. **/

	var $localize_array				= array();
	/** @var (associative_array)	Use to change the wp_editor settings. **/
	var $wp_editor_settings 		= array();
	static $notice_called			= false;
	static $error_message			= '';
	static $warning_message 		= '';
	static $success_message			= '';

	public function __construct() {
		// Setup menu
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		// Set ajax callback
		add_action( 'wp_ajax_' . $this->slug, array( $this, 'ajax_request' ) );
		add_action( 'coursepress_submitbox_misc_actions', array( __CLASS__, 'get_statuses' ), 10 );
	}

	public function get_labels() {
		return array(
			'title' => '',
			'menu_title' => '',
		);
	}

	public function admin_menu() {
		$labels = $this->get_labels();

		if ( empty( $labels ) ) {
			return;
		}

		if ( ! empty( $this->parent_slug ) ) {
			$post_type = CoursePress_Data_Course::get_post_type_name();
			$this->parent_slug = 'edit.php?post_type=' . $post_type;

			// It's a sub-menu
			$submenu = add_submenu_page( $this->parent_slug, $labels['title'], $labels['menu_title'], $this->cap, $this->slug, array( $this, 'render_page' ) );

			add_action( "load-{$submenu}", array( $this, 'before_page_load' ) );
			add_action( "load-{$submenu}", array( $this, 'process_form' ) );
		}
	}

	public function render_page() {
		$view_id = str_replace( 'coursepress_', '', $this->slug );
		$admin_path = dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;
		$view_file = $admin_path . $view_id . '.php';

		if ( is_readable( $view_file ) ) {
			require_once $view_file;
		}
	}

	public function before_page_load() {
		if ( ! current_user_can( $this->cap ) ) {
			wp_die( __( 'You have no permission to access this page!', 'CP_TD' ) );
		}

		// Set assets
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		// Admin notices
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

		$this->is_page_loaded = true;
	}

	/**
	 * Receives form submission
	 *
	 * Must be overriden in a sub-class
	 **/
	public function process_form() {}
	public function ajax_request() {}

	public function is_valid_page() {
		return isset( $_REQUEST[ $this->slug ] ) && wp_verify_nonce( $_REQUEST[ $this->slug ], $this->slug );
	}

	public static function init_tiny_mce_listeners( $init_array ) {
		$page = get_current_screen()->id;

		if ( $page == $this->slug ) {
			$init_array['height'] = '360px';
			$init_array['relative_urls'] = false;
			$init_array['url_converter'] = false;
			$init_array['url_converter_scope'] = false;

			$init_array['setup'] = 'function( ed ) {
				ed.on( \'keyup\', function( args ) {
					CoursePress.Events.trigger(\'editor:keyup\',ed);
				} );
			}';
		}

		return $init_array;
	}

	/**
	 * Select or set CSS and JS file to include in the page.
	 *
	 * Must be overriden in a sub-class
	 **/
	public function get_assets() {
		$this->scripts['admin-ui'] = true;
		$this->scripts['core'] = true;
		$this->scripts['jquery-select2'] = true;
		$this->css['admin-ui'] = true;
		$this->css['select2'] = true;
	}

	/**
	 * Sets CSS and JS assets needed for the page
	 **/
	public function assets() {
		if ( $this->is_page_loaded ) {
			$this->get_assets();

			$url = CoursePress::$url;
			$css_url = $url . 'asset/css/';
			$js_url = $url . 'asset/js/';
			$version = CoursePress::$version;
			$include_core = isset( $this->scripts['core'] );

			// Print styles
			$core_css = array(
				'select2' => $css_url . 'external/select2.min.css',
			);

			wp_enqueue_style( 'coursepress-admin-ui', $css_url . 'admin-ui.css', array(), $version );
			if ( $include_core ) {
				// Chosen
				wp_enqueue_style( 'cp_chosen_css', $css_url . 'external/chosen.css' );
				// Font Awesome.
				wp_enqueue_style( 'fontawesome', $css_url . 'external/font-awesome.min.css' );

				// General admin css
				wp_enqueue_style( 'coursepress_admin_general', $css_url . 'admin-general.css', array(), $version );
				wp_enqueue_style( 'coursepress_admin_global', $css_url . 'admin-global.css', array( 'dashicons' ), $version );
			}

			// Print the css required for this page
			foreach ( $this->css as $css_id => $css_path ) {
				if ( isset( $core_css[ $css_id ] ) ) {
					wp_deregister_style( $css_id );
					wp_enqueue_style( $css_id, $core_css[ $css_id ] );
				} else {
					if ( 1 != $css_path ) {
						wp_enqueue_style( "coursepress-{$css_id}", $css_path, array(), $version );
					}
				}
			}

			// Print scripts
			$dependencies = array( 'jquery', 'backbone', 'underscore' );

			$core_scripts = array(
				'jquery-select2' => $url . 'asset/js/external/select2.min.js',
				'admin-ui' => $url . 'asset/js/admin-ui.min.js',
			);

			if ( $include_core ) {
				// Load coursepress core scripts
				$course_dependencies = array(
					'jquery-ui-accordion',
					'jquery-effects-highlight',
					'jquery-effects-core',
					'jquery-ui-datepicker',
					'jquery-ui-spinner',
					'jquery-ui-droppable',
				);

				if ( isset( $this->scripts['jquery-select2'] ) ) {
					$course_dependencies[] = 'jquery-select2';
				}
				wp_enqueue_script( 'coursepress_object', $url . 'asset/js/coursepress.js', array( 'jquery', 'backbone', 'underscore' ), $version );
				wp_enqueue_script( 'chosen', $url . 'asset/js/external/chosen.jquery.min.js' );
				wp_enqueue_script( 'coursepress_course', $url . 'asset/js/coursepress-course.js', $course_dependencies, $version );
				wp_enqueue_script( 'jquery-treegrid', $url . 'asset/js/external/jquery.treegrid.min.js' );

				$script = $url . 'asset/js/admin-general.js';
				$sticky = $url . 'asset/js/external/sticky.min.js';

				wp_enqueue_script( 'coursepress_admin_general_js', $script, array( 'jquery' ), $version, true );
				wp_enqueue_script( 'sticky_js', $sticky, array( 'jquery' ), $version, true );
			}

			// Print the script required for this page
			foreach ( $this->scripts as $script_id => $script_path ) {
				if ( isset( $core_scripts[ $script_id ] ) ) {
					wp_deregister_script( $script_id );
					wp_enqueue_script( $script_id, $core_scripts[ $script_id ], array( 'jquery' ) );
				} else {
					if ( 1 != $script_path ) {
						wp_enqueue_script( "coursepress_{$script_id}", $script_path, false, $version );
					}
				}
			}

			if ( $include_core ) {
				$this->localize_array = array_merge(
					array(
						'_ajax_url' => CoursePress_Helper_Utility::get_ajax_url(),
						'allowed_video_extensions' => wp_get_video_extensions(),
						'allowed_audio_extensions' => wp_get_audio_extensions(),
						'allowed_image_extensions' => CoursePress_Helper_Utility::get_image_extensions(),
						'allowed_extensions' => apply_filters( 'coursepress_custom_allowed_extensions', false ),
						'date_format' => get_option( 'date_format' ),
						'editor_visual' => __( 'Visual', 'CP_TD' ),
						'editor_text' => _x( 'Text', 'Name for the Text editor tab (formerly HTML)', 'CP_TD' ),
						'invalid_extension_message' => __( 'Extension of the file is not valid. Please use one of the following:', 'CP_TD' ),
						'is_super_admin' => current_user_can( 'manage_options' ),
						'user_caps' => CoursePress_Data_Capabilities::get_user_capabilities(),
						'server_error' => __( 'An error occur while processing your request. Please try again later!', 'CP_TD' ),
						'labels' => array(
							'user_dropdown_placeholder' => __( 'Enter username, first name and last name, or email', 'CP_TD' ),
						),
						'messages' => array(
							'notification' => array(
								'empty_content' => __( 'No notification content!', 'CP_TD' ),
								'empty_title' => __( 'No notification title!', 'CP_TD' ),
							),
							'discussion' => array(
								'empty_content' => __( 'No thread content!', 'CP_TD' ),
								'empty_title' => __( 'No thread title!', 'CP_TD' ),
							),
							'general' => array(
								'empty_content' => __( 'No content!', 'CP_TD' ),
								'empty_title' => __( 'No title!', 'CP_TD' ),
							),
							'instructors' => array(
								'instructor_delete_confirm' => __( 'Please confirm that you want to remove the instructor from this course (%s).', 'CP_TD' ),
								'instructor_delete_all_confirm' => __( 'Please confirm that you want to remove the instructor from ALL the associated courses.', 'CP_TD' ),
							),
						),
					),
					$this->localize_array
				);

				if ( $this->with_editor ) {
					add_action( 'admin_footer', array( $this, 'prepare_editor' ), 1 );
				}

				wp_localize_script( 'coursepress_object', '_coursepress', $this->localize_array );
			}
		}
	}

	public function prepare_editor() {
		// Create a single wp-editor instance
		$this->wp_editor_settings = wp_parse_args(
			$this->wp_editor_settings,
			array(
					'textarea_name' => 'dummy_editor_name',
					'wpautop' => true,
				)
		);
		echo '<script type="text/template" id="cp-wp-editor">';
			wp_editor( 'dummy_editor_content', 'dummy_editor_id', $this->wp_editor_settings );
		echo '</script>';
	}

	public function admin_notices() {
		$format = '<div class="notice notice-%s is-dismissible"><p>%s</p></div>';

		if ( ! empty( self::$error_message ) ) {
			printf( $format, 'error', self::$error_message );
		}
		if ( ! empty( self::$warning_message ) ) {
			printf( $format, 'warning', self::$warning_message );
		}
		if ( ! empty( self::$success_message ) ) {
			printf( $format, 'success', self::$success_message );
		}
	}

	/**
	 * Set label properites.
	 *
	 * @since @2.0.0
	 */
	protected static function set_labels() {
		if ( ! isset( self::$labels[ self::$post_type ] ) || empty( self::$labels[ self::$post_type ] ) ) {
			$type_object = get_post_type_object( self::$post_type );
			self::$labels[ self::$post_type ] = get_post_type_labels( $type_object );
		}
	}

	/**
	 * Add new item button - only code, indepened on type
	 *
	 * @since @2.0.0
	 */
	protected static function button_add( $label ) {
		$url = remove_query_arg( 'id' );
		$url = add_query_arg( 'action', 'edit', $url );
		printf(
			'<a href="%s" class="page-title-action">%s</a>',
			esc_url( $url ),
			$label
		);
	}

	/**
	 * submitbox content
	 */
	protected static function submitbox( $post, $can_change_function ) {
		$post_id = 0;
		if ( is_object( $post ) ) {
			$post_id = $post->ID;
		} else {
			$post = new stdClass;
		}
		$post->can_change_status = call_user_func( array( 'CoursePress_Data_Capabilities', $can_change_function ), $post_id );
		if ( 'draft' == $post->post_status && $post->can_change_status ) {
?>
<div id="minor-publishing-actions">
<div id="save-action">
<input type="submit" name="save" id="save-post" value="<?php esc_attr_e( 'Save Draft', 'CP_TD' ); ?>" class="button">
<span class="spinner"></span>
</div>
<div class="clear"></div>
</div>
<?php
		}
		/**
		 * misc actions
		 */
		printf( '<div id="misc-publishing-actions" data-no-options="%s">', esc_attr__( 'no option available', 'CP_TD' ) );
		do_action( 'coursepress_submitbox_misc_actions', $post );
		echo '</div>';
		/**
		 * major actions
		 */
		echo '<div id="major-publishing-actions"><div id="publishing-action"><span class="spinner"></span>';
		$label = __( 'Publish', 'CP_TD' );
		if ( ! $post->can_change_status && empty( $post->ID ) ) {
			$label = __( 'Save', 'CP_TD' );
		}
		$class = 'force-publish';
		if ( 'publish' == $post->post_status || ! $post->can_change_status ) {
			$label = __( 'Update', 'CP_TD' );
			$class = '';
		}
		printf(
			'<input type="submit" class="button button-primary %s" value="%s" />',
			$class,
			esc_attr( $label )
		);
		echo '</div>';
		echo '<div class="clear"></div>';
		echo '</div>';
	}

	public static function get_statuses( $post ) {
		$allowed_statuses = array(
			'draft'		 => __( 'Draft', 'CP_TD' ),
			'publish'	   => __( 'Published', 'CP_TD' ),
		);
		if ( isset( $post ) ) {
			if ( ! array_key_exists( $post->post_status, $allowed_statuses ) ) {
				$post->post_status = 'draft';
			}
		} else {
			if ( ! is_object( $post ) ) {
				$post = new stdClass();
				$post->ID = 0;
			}
			$post->post_status = 'draft';
		}
?>
<div class="misc-pub-section misc-pub-post-status">
<label for="post_status"><?php _e( 'Status:' ) ?></label>
<span id="post-status-display">
<?php
switch ( $post->post_status ) {
	case 'private':
		_e( 'Privately Published' );
		break;
	case 'publish':
		_e( 'Published' );
		break;
	case 'future':
		_e( 'Scheduled' );
		break;
	case 'pending':
		_e( 'Pending Review' );
		break;
	case 'draft':
	case 'auto-draft':
	default:
		_e( 'Draft' );
		break;
}

?>
</span>
<?php
if ( $post->can_change_status ) {
?>
<a href="#post_status" <?php if ( 'private' == $post->post_status ) { ?>style="display:none;" <?php } ?>class="edit-post-status hide-if-no-js"><span aria-hidden="true"><?php _e( 'Edit' ); ?></span> <span class="screen-reader-text"><?php _e( 'Edit status' ); ?></span></a>

<div id="post-status-select" class="hide-if-js">
<input type="hidden" name="hidden_post_status" id="hidden_post_status" value="<?php echo esc_attr( ('auto-draft' == $post->post_status ) ? 'draft' : $post->post_status ); ?>" />
<select name='post_status' id='post_status'>
<?php
foreach ( $allowed_statuses as $status => $label ) {
	printf(
		'<option %s value="%s">%s</option>',
		selected( $post->post_status, $status ),
		esc_attr( $status ),
		$label
	);
}
?>
</select>
 <a href="#post_status" class="save-post-status hide-if-no-js button"><?php _e( 'OK' ); ?></a>
 <a href="#post_status" class="cancel-post-status hide-if-no-js button-cancel"><?php _e( 'Cancel' ); ?></a>
</div>
<?php } ?>
</div>
<?php
	}
}
