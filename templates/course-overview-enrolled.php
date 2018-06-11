<?php
/**
 * The template use to print course overview.
 *
 * @since 3.0
 * @package CoursePress
 */
$course = coursepress_get_course(); ?>
            <div class="content-area">
<?php
/**
 * To override course submenu template to your theme or a child-theme,
 * create a template `course-submenu.php` and it will be loaded instead.
 *
 * @since 3.0
 */
coursepress_get_template( 'course', 'submenu' );
coursepress_breadcrumb();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( '' ); ?>>
    <header class="entry-header">
        <h1 class="entry-title"><?php echo apply_filters( 'coursepress_schema', coursepress_get_course_title(), 'title' ); ?></h1>
	    <?php echo do_shortcode( '[course_category label="" no_category_show="hide"]' ); ?>
    </header>
    <div class="entry-content">
	<?php $messages = apply_filters( 'coursepress_overview_messages', array() ); ?>
	<?php if ( ! empty( $messages ) ) : ?>
		<div class="cp-warning-box">
		<?php foreach ( $messages as $message ) : ?>
			<p><?php echo $message; ?></p>
		<?php endforeach; ?>
		</div>
    <?php endif;

$media = coursepress_get_course_media( false, 440, 330 );
$class = '';
if ( ! empty( $media ) ) {
	$class = ' course-has-media';
}

?>
        <div class="course-details<?php echo esc_attr( $class ); ?>">
	<?php
	if ( ! empty( $media ) ) :
		?>
		<div class="course-media">
			<?php echo $media; ?>
        </div>
	<?php endif; ?>
		<div class="course-metas">
			<p class="course-meta">
				<span class="meta-title"><?php _e( 'Availability:', 'cp' ); ?></span>
				<span class="course-meta-dates"><?php echo coursepress_get_course_availability_dates(); ?></span>
			</p>
			<p class="course-meta">
				<span class="meta-title"><?php _e( 'Enrollment:', 'cp' ); ?></span>
				<span class="course-meta-enrollment-dates"><?php echo coursepress_get_course_enrollment_dates(); ?></span>
			</p>
			<p class="course-meta">
				<span class="meta-title"><?php _e( 'Who can Enroll:', 'cp' ); ?></span>
				<span class="course-meta-enrollment-dates"><?php echo coursepress_get_course_enrollment_type(); ?></span>
			</p>
<?php
	/**
	 * language
	 */
	$show = coursepress_get_setting( 'general/details_show_language', 1 );
if ( $show ) {
?>
		<p class="course-meta">
			<span class="meta-title"><?php _e( 'Language:', 'cp' ); ?></span>
			<span class="course-meta course-meta-language"><?php echo $course->get_course_language(); ?></span>
		</p>
<?php
}
?>
			</div>
		</div>

		<div class="course-description"<?php echo apply_filters( 'coursepress_schema', '', 'description' ); ?>>
			<h3 class="sub-title course-sub-title"><?php _e( 'About this course', 'cp' ); ?></h3>
			<?php echo apply_filters( 'the_content', coursepress_get_course_description() ); ?>
		</div>

<?php
				$show = coursepress_get_setting( 'general/details_show_instructors', 1 );
if ( $show ) {
	echo do_shortcode( '[course_instructors course_id="' . $course->ID . '"]' );
}
?>

	</div>
</article>
</div>
