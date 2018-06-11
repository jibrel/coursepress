<?php
/**
 * The template use for courses list.
 *
 * @since 3.0
 * @package CoursePress
 *
 * @var $post CoursePress_Course
 */
global $post;
$course         = coursepress_get_course( $post );
$thumbnail      = $course->get_feature_image( 'course-thumbnail' );
$course_media   = do_shortcode( '[course_media wrapper="figure" list_page="yes"]' );
$extended_class = $course_media ? '' : 'quick-course-info-extended';
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'course-item-box' ); ?>>
	<div class="course-info"<?php echo apply_filters( 'coursepress_schema', '', 'itemscope' ); ?>>
<?php
if ( ! empty( $thumbnail ) ) {
	echo '<div class="thumbnail">';
	printf( '<a href="%s" class="post-thumbnail" aria-hidden="true">%s</a>', esc_url( $course->get_permalink() ), $thumbnail );
	echo '</div>';
}
?>
<div class="course-entry-wrap">
<header class="entry-header course-entry-header">
	<?php $title = apply_filters( 'coursepress_schema', get_the_title(), 'title' ); ?>
	<h3 class="entry-title course-title"><a href="<?php echo esc_url( $course->get_permalink() ); ?>" rel="bookmark"><?php echo $title; ?></a></h3>
    <div class="instructors-content"<?php echo apply_filters( 'coursepress_schema', '', 'itemscope-person' ); ?>>
<?php
// Flat hyperlinked list of instructors
echo do_shortcode( '[course_instructors style="list-flat" link="true"]' );
?>
    </div>
</header>
<?php if ( is_search() ) : // Only display Excerpts for Search ?>
<div class="entry-summary">
<?php the_excerpt(); ?>
</div><!-- .entry-summary -->
<?php else : ?>
<div class="entry-content <?php echo $extended_class; ?>">
<?php
// Course summary/excerpt
echo do_shortcode( '[course_summary length="50" class="' . $extended_class . '"]' );
wp_link_pages(
	array(
		'before' => '<div class="page-links">' . __( 'Pages:', 'cp' ),
		'after' => '</div>',
	)
);
?>
</div><!-- .entry-content -->
<footer class="entry-footer">
    <div class="quick-course-info <?php echo ( isset( $extended_class ) ? $extended_class : '' ); ?>">
<?php
echo do_shortcode( '[course_start label="" class="course-time"]' );
$show = coursepress_get_setting( 'general/listing_show_language', 1 );
if ( $show ) {
	echo do_shortcode( '[course_language label="" class="course-lang"]' );
}
	echo do_shortcode( '[course_category label="" tag="span" no_category_show="hide"]' );
$show = coursepress_get_setting( 'general/listing_show_price_free', 1 );
$is_paid = $course->is_paid_course();
if ( $show || $is_paid ) {
	echo do_shortcode( '[course_cost label="" show_icon="true" tag="span"]' );
}
echo do_shortcode( '[course_join_button details_text="' . __( 'Details', 'cp' ) . '" course_expired_text="' . __( 'Not Available', 'cp' ) . '" list_page="yes"]' );
?>
<!--go-to-course-button-->
        </div>
    </div>
</footer>
		<?php endif; ?>
	</div>
</article>
