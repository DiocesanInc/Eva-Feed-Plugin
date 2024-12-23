<?php

/**
 * The template for displaying all "Eva" posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header();

get_template_part("template-parts/headers/page-header"); ?>

<div class="content-area" id="primary">
    <div class="site-main entry-content limit-width" id="main">
        <div class="single-container">

            <?php
            /* Start the Loop */
            while (have_posts()) : the_post();
                the_content();

            endwhile; // End of the loop.
            ?>

        </div>
    </div>
</div>

<?php get_footer();
