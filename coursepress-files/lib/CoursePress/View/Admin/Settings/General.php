<?php

class CoursePress_View_Admin_Settings_General {

	public static function init() {

		add_filter( 'coursepress_settings_tabs', array( __CLASS__, 'add_tabs' ) );
		add_action( 'coursepress_settings_process_general', array( __CLASS__, 'process_form' ), 10, 2 );
		add_filter( 'coursepress_settings_render_tab_general', array( __CLASS__, 'return_content' ), 10, 3 );
	}


	public static function add_tabs( $tabs ) {

		$tabs['general'] = array(
			'title'       => __( 'General Settings', CoursePress::TD ),
			'description' => __( 'Configure the general settings for CoursePress.', CoursePress::TD ),
			'order'       => 0 // first tab
		);

		return $tabs;
	}

	public static function return_content( $content, $slug, $tab ) {

		$my_course_prefix = __( 'my-course', CoursePress::TD );

		$page_dropdowns = array();

		$pages_args     = array(
			'selected'          => CoursePress_Core::get_setting( 'pages/enrollment', 0 ),
			'echo'              => 0,
			'show_option_none'  => __( 'Use virtual page', CoursePress::TD ),
			'option_none_value' => 0,
			'name'              => 'coursepress_settings[pages][enrollment]'
		);
		$page_dropdowns['enrollment'] = wp_dropdown_pages( $pages_args );

		$pages_args['selected'] = CoursePress_Core::get_setting( 'pages/login', 0 );
		$pages_args['name'] = 'coursepress_settings[pages][login]';
		$page_dropdowns['login'] = wp_dropdown_pages( $pages_args );

		$pages_args['selected'] = CoursePress_Core::get_setting( 'pages/signup', 0 );
		$pages_args['name'] = 'coursepress_settings[pages][signup]';
		$page_dropdowns['signup'] = wp_dropdown_pages( $pages_args );

		$pages_args['selected'] = CoursePress_Core::get_setting( 'pages/student_dashboard', 0 );
		$pages_args['name'] = 'coursepress_settings[pages][student_dashboard]';
		$page_dropdowns['student_dashboard'] = wp_dropdown_pages( $pages_args );

		$pages_args['selected'] = CoursePress_Core::get_setting( 'pages/student_settings', 0 );
		$pages_args['name'] = 'coursepress_settings[pages][student_settings]';
		$page_dropdowns['student_settings'] = wp_dropdown_pages( $pages_args );


		$content = '
			<input type="hidden" name="page" value="' . esc_attr( $slug ) . '"/>
			<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '"/>
			<input type="hidden" name="action" value="updateoptions"/>
		' . wp_nonce_field( 'update-coursepress-options', '_wpnonce', true, false ) . '
				<!-- SLUGS -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Slugs', CoursePress::TD ) . '</span></h3>
				<p class="description">' . sprintf( __( 'A slug is a few words that describe a post or a page. Slugs are usually a URL friendly version of the post title ( which has been automatically generated by WordPress ), but a slug can be anything you like. Slugs are meant to be used with %s as they help describe what the content at the URL is. Post slug substitutes the %s placeholder in a custom permalink structure.', CoursePress::TD ), '<a href="options-permalink.php">permalinks</a>', '<strong>"%posttitle%"</strong>' ) . '</p>
				<div class="inside">

					<table class="form-table slug-settings">
						<tbody>
							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Courses Slug', CoursePress::TD ) . '</th>
								<td>' . esc_html( trailingslashit( home_url() ) ) . '
									&nbsp;<input type="text" name="coursepress_settings[slugs][course]" id="course_slug" value="' . esc_attr( CoursePress_Core::get_setting( 'slugs/course' ) ) . '" />&nbsp;/
									<p class="description">' . esc_html( 'Your course URL will look like: ', CoursePress::TD ) . esc_html( trailingslashit( home_url() ) ) . esc_html( CoursePress_Core::get_setting( 'slugs/course' ) ) . esc_html( '/my-course/', CoursePress::TD ) . '</p>
								</td>
							</tr>
							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Course Category Slug', CoursePress::TD ) . '</th>
								<td>' . esc_html( trailingslashit( home_url() ) . trailingslashit( esc_html( CoursePress_Core::get_setting( 'slugs/course' ) ) ) ) . '
									&nbsp;<input type="text" name="coursepress_settings[slugs][category]" id="category_slug" value="' . esc_attr( CoursePress_Core::get_setting( 'slugs/category' ) ) . '" />&nbsp;/
									<p class="description">' . esc_html__( 'Your course category URL will look like: ', CoursePress::TD ) . trailingslashit( esc_url( home_url() ) ) . esc_html( CoursePress_Core::get_setting( 'slugs/course' ) . '/' . CoursePress_Core::get_setting( 'slugs/category' ) ) . esc_html__( '/your-category/', CoursePress::TD ) . '</p>
								</td>
							</tr>
							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Units Slug', CoursePress::TD ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . trailingslashit( esc_html( $my_course_prefix ) ) . '
									&nbsp;<input type="text" name="coursepress_settings[slugs][units]" id="units_slug" value="' . esc_attr( CoursePress_Core::get_setting( 'slugs/units' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Course Notifications Slug', CoursePress::TD ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . trailingslashit( esc_html( $my_course_prefix ) ) . '
									&nbsp;<input type="text" name="coursepress_settings[slugs][notifications]" id="notifications_slug" value="' . esc_attr( CoursePress_Core::get_setting( 'slugs/notifications' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Course Discussions Slug', CoursePress::TD ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . trailingslashit( esc_html( $my_course_prefix ) ) . '
									&nbsp;<input type="text" name="coursepress_settings[slugs][discussions]" id="discussions_slug" value="' . esc_attr( CoursePress_Core::get_setting( 'slugs/discussions' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">' . esc_html__( 'Course New Discussion Slug', CoursePress::TD ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . trailingslashit( esc_html( $my_course_prefix ) ) . trailingslashit( esc_attr( CoursePress_Core::get_setting( 'slugs/discussions' ) ) ) .'
									&nbsp;<input type="text" name="coursepress_settings[slugs][discussions_new]" id="discussions_new_slug" value="' . esc_attr( CoursePress_Core::get_setting( 'slugs/discussions_new' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Course Grades Slug', CoursePress::TD ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . trailingslashit( esc_html( $my_course_prefix ) ) . '
									&nbsp;<input type="text" name="coursepress_settings[slugs][grades]" id="grades_slug" value="' . esc_attr( CoursePress_Core::get_setting( 'slugs/grades' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Course Workbook Slug', CoursePress::TD ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . trailingslashit( esc_html( $my_course_prefix ) ) . '
									&nbsp;<input type="text" name="coursepress_settings[slugs][workbook]" id="workbook_slug" value="' . esc_attr( CoursePress_Core::get_setting( 'slugs/workbook' ) ) . '" />&nbsp;/
								</td>
							</tr>

							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Enrollment Process Slug', CoursePress::TD ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . '
									&nbsp;<input type="text" name="coursepress_settings[slugs][enrollment]" id="enrollment_slug" value="' . esc_attr( CoursePress_Core::get_setting( 'slugs/enrollment' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">' . esc_html__( 'Enrollment Process Page', CoursePress::TD ) . '</th>
								<td>' .
						            $page_dropdowns['enrollment'] .
		                            '<p class="description">' . sprintf( __( 'Select page where you have %s shortcode or any other set of %s. Please note that slug for the page set above will not be used if "Use virtual page" is not selected.', CoursePress::TD ), '<strong>[cp_pages page="enrollment_process"]</strong>', '<a target="_blank" href="' . admin_url( 'admin.php?page=' . $_GET['page'] . '&tab=shortcodes' ) . '">' . __( 'shortcodes', CoursePress::TD ) . '</a>' ) . '</p>
		                        </td>
							</tr>

							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Login Slug', CoursePress::TD ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . '
									&nbsp;<input type="text" name="coursepress_settings[slugs][login]" id="login_slug" value="' . esc_attr( CoursePress_Core::get_setting( 'slugs/login' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">' . esc_html__( 'Login Page', CoursePress::TD ) . '</th>
								<td>' .
						           $page_dropdowns['login'] .
						           '<p class="description">' . sprintf( __( 'Select page where you have %s shortcode or any other set of %s. Please note that slug for the page set above will not be used if "Use virtual page" is not selected.', CoursePress::TD ), '<strong>[cp_pages page="student_login"]</strong>', '<a target="_blank" href="' . admin_url( 'admin.php?page=' . $_GET['page'] . '&tab=shortcodes' ) . '">' . __( 'shortcodes', CoursePress::TD ) . '</a>' ) . '</p>
		                        </td>
							</tr>

							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Signup Slug', CoursePress::TD ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . '
									&nbsp;<input type="text" name="coursepress_settings[slugs][signup]" id="signup_slug" value="' . esc_attr( CoursePress_Core::get_setting( 'slugs/signup' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">' . esc_html__( 'Signup Page', CoursePress::TD ) . '</th>
								<td>' .
						           $page_dropdowns['signup'] .
						           '<p class="description">' . sprintf( __( 'Select page where you have %s shortcode or any other set of %s. Please note that slug for the page set above will not be used if "Use virtual page" is not selected.', CoursePress::TD ), '<strong>[cp_pages page="student_signup"]</strong>', '<a target="_blank" href="' . admin_url( 'admin.php?page=' . $_GET['page'] . '&tab=shortcodes' ) . '">' . __( 'shortcodes', CoursePress::TD ) . '</a>' ) . '</p>
		                        </td>
							</tr>

							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Student Dashboard Slug', CoursePress::TD ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . '
									&nbsp;<input type="text" name="coursepress_settings[slugs][student_dashboard]" id="student_dashboard_slug" value="' . esc_attr( CoursePress_Core::get_setting( 'slugs/student_dashboard' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">' . esc_html__( 'Student Dashboard Page', CoursePress::TD ) . '</th>
								<td>' .
						           $page_dropdowns['student_dashboard'] .
						           '<p class="description">' . sprintf( __( 'Select page where you have %s shortcode or any other set of %s. Please note that slug for the page set above will not be used if "Use virtual page" is not selected.', CoursePress::TD ), '<strong>[cp_pages page="student_dashboard"]</strong>', '<a target="_blank" href="' . admin_url( 'admin.php?page=' . $_GET['page'] . '&tab=shortcodes' ) . '">' . __( 'shortcodes', CoursePress::TD ) . '</a>' ) . '</p>
		                        </td>
							</tr>

							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Student Dashboard Slug', CoursePress::TD ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . '
									&nbsp;<input type="text" name="coursepress_settings[slugs][student_settings]" id="student_settings_slug" value="' . esc_attr( CoursePress_Core::get_setting( 'slugs/student_settings' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">' . esc_html__( 'Student Dashboard Page', CoursePress::TD ) . '</th>
								<td>' .
						           $page_dropdowns['student_settings'] .
						           '<p class="description">' . sprintf( __( 'Select page where you have %s shortcode or any other set of %s. Please note that slug for the page set above will not be used if "Use virtual page" is not selected.', CoursePress::TD ), '<strong>[cp_pages page="student_settings"]</strong>', '<a target="_blank" href="' . admin_url( 'admin.php?page=' . $_GET['page'] . '&tab=shortcodes' ) . '">' . __( 'shortcodes', CoursePress::TD ) . '</a>' ) . '</p>
		                        </td>
							</tr>

							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Instructor Profile Slug', CoursePress::TD ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . '
									&nbsp;<input type="text" name="coursepress_settings[slugs][instructor_profile]" id="instructor_profile_slug" value="' . esc_attr( CoursePress_Core::get_setting( 'slugs/instructor_profile' ) ) . '" />&nbsp;/
								</td>
							</tr>';


		if ( function_exists( 'messaging_init' ) ) {

			$content .= '
							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Messaging: Inbox Slug', CoursePress::TD ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . '
									&nbsp;<input type="text" name="coursepress_settings[slugs][inbox]" id="inbox_slug" value="' . esc_attr( CoursePress_Core::get_setting( 'slugs/inbox' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">' . esc_html__( 'Sent Messages Slug', CoursePress::TD ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . '
									&nbsp;<input type="text" name="coursepress_settings[slugs][sent_messages]" id="sent_messages" value="' . esc_attr( CoursePress_Core::get_setting( 'slugs/sent_messages' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">' . esc_html__( 'New Messages Slug', CoursePress::TD ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . '
									&nbsp;<input type="text" name="coursepress_settings[slugs][new_messages]" id="new_messages_slug" value="' . esc_attr( CoursePress_Core::get_setting( 'slugs/new_messages' ) ) . '" />&nbsp;/
								</td>
							</tr>
			';

		}


		$content .= '
						</tbody>
					</table>


				</div>

				<!-- THEME MENU ITEMS -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Theme Menu Items', CoursePress::TD ) . '</span></h3>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Display Menu Items', CoursePress::TD ) . '
									<a class="help-icon" href="javascript:;"></a>
									<div class="tooltip hidden">
										<div class="tooltip-before"></div>
										<div class="tooltip-button">&times;</div>
										<div class="tooltip-content">
											' . __( '<div>Attach default CoursePress menu items ( Courses, Student Dashboard, Log Out ) to the <strong>Primary Menu</strong>.</div><div>Items can also be added from Appearance > Menus and the CoursePress panel.</div>', CoursePress::TD ) . '
										</div>
									</div>
								</th>
								<td>';

									$checked = CoursePress_Core::get_setting( 'general/show_coursepress_menu', 1 ) ? 'checked' : '';
		$content .= '
									<input type="checkbox" name="coursepress_settings[general][show_coursepress_menu]" ' . $checked  . ' />
									';

		if ( current_user_can( 'manage_options' ) ) {
			$menu_error = true;
			$locations  = get_theme_mod( 'nav_menu_locations' );
			if ( is_array( $locations ) ) {
				foreach ( $locations as $location => $value ) {
					if ( $value > 0 ) {
						$menu_error = false; //at least one is defined
					}
				}
			}
			if( $menu_error ) {

				$content .= '
									<span class="settings-error">
									' . __( 'Please add at least one menu and select its theme location in order to show CoursePress menu items automatically.', CoursePress::TD ) . '
									</span>
				';

			}
		}


		$content .= '
								</td>
							</tr>
						</tbody>
					</table>

				</div>

				<!-- LOGIN FORM -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Login Form', CoursePress::TD ) . '</span></h3>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Use Custom Login Form', CoursePress::TD ) . '
									<a class="help-icon" href="javascript:;"></a>
									<div class="tooltip hidden">
										<div class="tooltip-before"></div>
										<div class="tooltip-button">&times;</div>
										<div class="tooltip-content">
											' . __( 'Uses a custom Login Form to keep students on the front-end of your site.', CoursePress::TD ) . '
										</div>
									</div>
								</th>
								<td>';

		$checked = CoursePress_Core::get_setting( 'general/use_custom_login', 1 ) ? 'checked' : '';
		$content .= '
									<input type="checkbox" name="coursepress_settings[general][use_custom_login]" ' . $checked  . ' />
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- WP LOGING REDIRECTION -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'WordPress Login Redirect', CoursePress::TD ) . '</span></h3>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Redirect After Login', CoursePress::TD ) . '
									<a class="help-icon" href="javascript:;"></a>
									<div class="tooltip hidden">
										<div class="tooltip-before"></div>
										<div class="tooltip-button">&times;</div>
										<div class="tooltip-content">
											' . __(  'Redirect students to their Dashboard upon login via wp-login form.', CoursePress::TD ) . '
										</div>
									</div>
								</th>
								<td>';

		$checked = CoursePress_Core::get_setting( 'general/redirect_after_login', 1 ) ? 'checked' : '';
		$content .= '
									<input type="checkbox" name="coursepress_settings[general][redirect_after_login]" ' . $checked  . ' />
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- PRIVACY -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Privacy', CoursePress::TD ) . '</span></h3>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Show Instructor Username in URL', CoursePress::TD ) . '
									<a class="help-icon" href="javascript:;"></a>
									<div class="tooltip hidden">
										<div class="tooltip-before"></div>
										<div class="tooltip-button">&times;</div>
										<div class="tooltip-content">
											' . __(  'If checked, instructors username will be shown in the url. Otherwise, hashed (MD5) version will be shown.', CoursePress::TD ) . '
										</div>
									</div>
								</th>
								<td>';

		$checked = CoursePress_Core::get_setting( 'instructor/show_username', 1 ) ? 'checked' : '';
		$content .= '
									<input type="checkbox" name="coursepress_settings[instructor][show_username]" ' . $checked  . ' />
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- COURSE DETAILS PAGE -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Course Details Page', CoursePress::TD ) . '</span></h3>
				<p class="description">' . __( 'Media to use when viewing course details.', CoursePress::TD ) . '</p>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Media Type', CoursePress::TD ) . '
									<a class="help-icon" href="javascript:;"></a>
									<div class="tooltip hidden">
										<div class="tooltip-before"></div>
										<div class="tooltip-button">&times;</div>
										<div class="tooltip-content">
											' . __(  '"Priority" - Use the media type below, with the other type as a fallback.', CoursePress::TD ) . '
										</div>
									</div>
								</th>
								<td>';

		$selected_type = CoursePress_Core::get_setting( 'course/details_media_type', 'default' );
		$content .= '
									<select name="coursepress_settings[course][details_media_type]" class="widefat" id="course_details_media_type">
										<option value="default" ' . selected( $selected_type, 'default', false ) .'>' . __( 'Priority Mode (default)', CoursePress::TD ) . '</option>
										<option value="video" ' . selected( $selected_type, 'video', false ) .'>' . __( 'Featured Video', CoursePress::TD ) . '</option>
										<option value="image" ' . selected( $selected_type, 'image', false ) .'>' . __( 'List Image', CoursePress::TD ) . '</option>
									</select>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Priority', CoursePress::TD ) . '
									<a class="help-icon" href="javascript:;"></a>
									<div class="tooltip hidden">
										<div class="tooltip-before"></div>
										<div class="tooltip-button">&times;</div>
										<div class="tooltip-content">
											' . __(  'Example: Using "video", the featured video will be used if available. The listing image is a fallback.', CoursePress::TD ) . '
										</div>
									</div>
								</th>
								<td>';

		$selected_priority = CoursePress_Core::get_setting( 'course/details_media_priority', 'default' );
		$content .= '
									<select name="coursepress_settings[course][details_media_priority]" class="widefat" id="course_details_media_priority">
										<option value="video" ' . selected( $selected_priority, 'video', false ) .'>' . __( 'Featured Video (image fallback)', CoursePress::TD ) . '</option>
										<option value="image" ' . selected( $selected_priority, 'image', false ) .'>' . __( 'List Image (video fallback)', CoursePress::TD ) . '</option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- COURSE LISTINGS -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Course Listings', CoursePress::TD ) . '</span></h3>
				<p class="description">' . __( 'Media to use when viewing course listings (e.g. Courses page or Instructor page).', CoursePress::TD ) . '</p>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Media Type', CoursePress::TD ) . '
									<a class="help-icon" href="javascript:;"></a>
									<div class="tooltip hidden">
										<div class="tooltip-before"></div>
										<div class="tooltip-button">&times;</div>
										<div class="tooltip-content">
											' . __(  '"Priority" - Use the media type below, with the other type as a fallback.', CoursePress::TD ) . '
										</div>
									</div>
								</th>
								<td>';

		$selected_type = CoursePress_Core::get_setting( 'course/listing_media_type', 'default' );
		$content .= '
									<select name="coursepress_settings[course][listing_media_type]" class="widefat" id="course_listing_media_type">
										<option value="default" ' . selected( $selected_type, 'default', false ) .'>' . __( 'Priority Mode (default)', CoursePress::TD ) . '</option>
										<option value="video" ' . selected( $selected_type, 'video', false ) .'>' . __( 'Featured Video', CoursePress::TD ) . '</option>
										<option value="image" ' . selected( $selected_type, 'image', false ) .'>' . __( 'List Image', CoursePress::TD ) . '</option>
									</select>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Priority', CoursePress::TD ) . '
									<a class="help-icon" href="javascript:;"></a>
									<div class="tooltip hidden">
										<div class="tooltip-before"></div>
										<div class="tooltip-button">&times;</div>
										<div class="tooltip-content">
											' . __(  'Example: Using "video", the featured video will be used if available. The listing image is a fallback.', CoursePress::TD ) . '
										</div>
									</div>
								</th>
								<td>';

		$selected_priority = CoursePress_Core::get_setting( 'course/listing_media_priority', 'default' );
		$content .= '
									<select name="coursepress_settings[course][listing_media_priority]" class="widefat" id="course_listing_media_priority">
										<option value="video" ' . selected( $selected_priority, 'video', false ) .'>' . __( 'Featured Video (image fallback)', CoursePress::TD ) . '</option>
										<option value="image" ' . selected( $selected_priority, 'image', false ) .'>' . __( 'List Image (video fallback)', CoursePress::TD ) . '</option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>

				</div>

				<!-- COURSE IMAGES -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Course Images', CoursePress::TD ) . '</span></h3>
				<p class="description">' . __( 'Size for (newly uploaded) course images.', CoursePress::TD ) . '</p>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Image Width', CoursePress::TD ) . '
								</th>
								<td>
									<input type="text" name="coursepress_settings[course][image_width]" value="' . esc_attr( CoursePress_Core::get_setting( 'course/image_width', 235 ) ) . '"/>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Image Height', CoursePress::TD ) . '
								</th>
								<td>
									<input type="text" name="coursepress_settings[course][image_height]" value="' . esc_attr( CoursePress_Core::get_setting( 'course/image_height', 225 ) ) . '"/>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- COURSE ORDER -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Course Order', CoursePress::TD ) . '</span></h3>
				<p class="description">' . __( 'Order of courses in admin and on front. If you choose "Post Order Number", you will have option to reorder courses from within the Courses admin page.', CoursePress::TD ) . '</p>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Order by', CoursePress::TD ) . '
								</th>
								<td>';

		$selected_order = CoursePress_Core::get_setting( 'course/order_by', 'post_date' );
		$content .= '
									<select name="coursepress_settings[course][order_by]" class="widefat" id="course_order_by">
										<option value="post_date" ' . selected( $selected_order, 'post_date', false ) .'>' . __( 'Post Date', CoursePress::TD ) . '</option>
										<option value="course_order" ' . selected( $selected_order, 'course_order', false ) .'>' . __( 'Post Order Number', CoursePress::TD ) . '</option>
									</select>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Direction', CoursePress::TD ) . '
								</th>
								<td>';

		$selected_dir = CoursePress_Core::get_setting( 'course/order_by_direction', 'DESC' );
		$content .= '
									<select name="coursepress_settings[course][order_by_direction]" class="widefat" id="course_order_by_direction">
										<option value="DESC" ' . selected( $selected_dir, 'DESC', false ) .'>' . __( 'Descending', CoursePress::TD ) . '</option>
										<option value="ASC" ' . selected( $selected_dir, 'ASC', false ) .'>' . __( 'Ascending', CoursePress::TD ) . '</option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- REPORTS -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Reports', CoursePress::TD ) . '</span></h3>
				<p class="description">' . __( 'Select font which will be used in the PDF reports.', CoursePress::TD ) . '</p>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Font', CoursePress::TD ) . '
								</th>
								<td>';

		$reports_font = CoursePress_Core::get_setting( 'reports/font', 'helvetica' );
		$content .= '
									<select name="coursepress_settings[reports][font]" class="widefat" id="course_order_by_direction">
										<option value="aealarabiya" ' . selected( $reports_font, "aealarabiya", false ) . '>' . __( "Al Arabiya", CoursePress::TD ) . '</option>
										<option value="aefurat" ' . selected( $reports_font, "aefurat", false ) . '>' . __( "Furat", CoursePress::TD ) . '</option>
										<option value="cid0cs" ' . selected( $reports_font, "cid0cs", false ) . '>' . __( "Arial Unicode MS (Simplified Chinese)", CoursePress::TD ) . '</option>
										<option value="cid0jp" ' . selected( $reports_font, "cid0jp", false ) . '>' . __( "Arial Unicode MS (Japanese)", CoursePress::TD ) . '</option>
										<option value="cid0kr" ' . selected( $reports_font, "cid0kr", false ) . '>' . __( "Arial Unicode MS (Korean)", CoursePress::TD ) . '</option>
										<option value="courier" ' . selected( $reports_font, "courier", false ) . '>' . __( "Courier", CoursePress::TD ) . '</option>
										<option value="dejavusans" ' . selected( $reports_font, "dejavusans", false ) . '>' . __( "DejaVu Sans", CoursePress::TD ) . '</option>
										<option value="dejavusanscondensed" ' . selected( $reports_font, "dejavusanscondensed", false ) . '>' . __( "DejaVu Sans Condensed", CoursePress::TD ) . '</option>
										<option value="dejavusansextralight" ' . selected( $reports_font, "dejavusansextralight", false ) . '>' . __( "DejaVu Sans ExtraLight", CoursePress::TD ) . '</option>
										<option value="dejavusansmono" ' . selected( $reports_font, "dejavusansmono", false ) . '>' . __( "DejaVu Sans Mono", CoursePress::TD ) . '</option>
										<option value="dejavuserif" ' . selected( $reports_font, "dejavuserif", false ) . '>' . __( "DejaVu Serif", CoursePress::TD ) . '</option>
										<option value="dejavuserifcondensed" ' . selected( $reports_font, "dejavuserifcondensed", false ) . '>' . __( "DejaVu Serif Condensed", CoursePress::TD ) . '</option>
										<option value="freemono" ' . selected( $reports_font, "freemono", false ) . '>' . __( "FreeMono", CoursePress::TD ) . '</option>
										<option value="freesans" ' . selected( $reports_font, "freesans", false ) . '>' . __( "FreeSans", CoursePress::TD ) . '</option>
										<option value="freeserif" ' . selected( $reports_font, "freeserif", false ) . '>' . __( "FreeSerif", CoursePress::TD ) . '</option>
										<option value="helvetica" ' . selected( $reports_font, "helvetica", false ) . '>' . __( "Helvetica", CoursePress::TD ) . '</option>
										<option value="hysmyeongjostdmedium" ' . selected( $reports_font, "hysmyeongjostdmedium", false ) . '>' . __( "MyungJo Medium (Korean)", CoursePress::TD ) . '</option>
										<option value="kozgopromedium" ' . selected( $reports_font, "kozgopromedium", false ) . '>' . __( "Kozuka Gothic Pro (Japanese Sans-Serif)", CoursePress::TD ) . '</option>
										<option value="kozminproregular" ' . selected( $reports_font, "kozminproregular", false ) . '>' . __( "Kozuka Mincho Pro (Japanese Serif)", CoursePress::TD ) . '</option>
										<option value="msungstdlight" ' . selected( $reports_font, "msungstdlight", false ) . '>' . __( "MSung Light (Traditional Chinese)", CoursePress::TD ) . '</option>
										<option value="pdfacourier" ' . selected( $reports_font, "pdfacourier", false ) . '>' . __( "PDFA Courier", CoursePress::TD ) . '</option>
										<option value="pdfahelvetica" ' . selected( $reports_font, "pdfahelvetica", false ) . '>' . __( "PDFA Helvetica", CoursePress::TD ) . '</option>
										<option value="pdfasymbol" ' . selected( $reports_font, "pdfasymbol", false ) . '>' . __( "PDFA Symbol", CoursePress::TD ) . '</option>
										<option value="pdfatimes" ' . selected( $reports_font, "pdfatimes", false ) . '>' . __( "PDFA Times", CoursePress::TD ) . '</option>
										<option value="pdfazapfdingbats" ' . selected( $reports_font, "pdfazapfdingbats", false ) . '>' . __( "PDFA Zapfdingbats", CoursePress::TD ) . '</option>
										<option value="stsongstdlight" ' . selected( $reports_font, "stsongstdlight", false ) . '>' . __( "STSong Light (Simplified Chinese)", CoursePress::TD ) . '</option>
										<option value="symbol" ' . selected( $reports_font, "symbol", false ) . '>' . __( "Symbol", CoursePress::TD ) . '</option>
										<option value="times" ' . selected( $reports_font, "times", false ) . '>' . __( "Times-Roman", CoursePress::TD ) . '</option>
										<option value="zapfdingbats" ' . selected( $reports_font, "zapfdingbats", false ) . '>' . __( "ZapfDingbats", CoursePress::TD ) . '</option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

		';

		return $content;

	}

	public static function process_form( $page, $tab ) {

		if ( isset( $_POST['action'] ) && 'updateoptions' === $_POST['action'] && 'general' === $tab && wp_verify_nonce( $_POST['_wpnonce'], 'update-coursepress-options' ) ) {

			$settings      = CoursePress_Core::get_setting( false ); // false returns all settings
			$post_settings = (array) $_POST['coursepress_settings'];

			// Now is a good time to make changes to $post_settings, especially to fix up unchecked checkboxes
			$post_settings['general']['show_coursepress_menu'] = isset($post_settings['general']['show_coursepress_menu']) ? : false;
			$post_settings['general']['use_custom_login'] = isset($post_settings['general']['use_custom_login']) ? : false;
			$post_settings['general']['redirect_after_login'] = isset($post_settings['general']['redirect_after_login']) ? : false;
			$post_settings['instructor']['show_username'] = isset( $post_settings['instructor']['show_username'] ) ? $post_settings['instructor']['show_username'] : false;

			// Don't replace settings if there is nothing to replace
			if ( ! empty( $post_settings ) ) {
				CoursePress_Core::update_setting( false, CoursePress_Core::merge_settings( $settings, $post_settings ) ); // false will replace all settings
			}

		}

	}

}