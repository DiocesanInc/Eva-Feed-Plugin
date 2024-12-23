<?php
/**
 * The template for displaying all "Eva" excerpts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */
 
$content = get_the_content();
$length = 50;
$words = str_word_count( wp_trim_words( $content, $length ) );
$readmore = true;
if( has_post_thumbnail() ) {
	$length = 20;
	if( $words < $length ) {
		$readmore = false;
	}
} elseif ( $words < $length ) {
	$readmore = false;
} ?>

<div class="dpi_eva_item" data-link="<?php the_permalink(); ?>">
	<div class="dpi_eva_item_title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
	<div class="dpi_eva_item_date"><?php the_time('F j, Y'); ?></div>
	<?php if( has_post_thumbnail() ) { ?>
	<div class="dpi_eva_item_featured_image"><?php the_post_thumbnail( 'eva-carousel' ); ?></div>
	<?php } ?>
	<div class="dpi_eva_item_excerpt"><?php echo wp_trim_words($content, $length); ?></div>
	<?php if( $readmore ) { ?>
	<div class="dpi_eva_item_read_more"><a href="<?php the_permalink(); ?>">Read More</a></div>			
	<?php } ?>
</div>
