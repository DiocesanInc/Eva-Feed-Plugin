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

get_template_part("template-parts/headers/page-header");
?>

<div id="primary" class="content-area">
    <main class="site-main archive" id="main">
        <?php

        if (have_posts()) : ?>

        <div id="eva-archive">

            <?php

                /* Start the Loop */

                while (have_posts()) : the_post();


                    include DPI_EVA_FEED_DIR . '/templates/excerpt-eva.php';

                endwhile;

                ?>

        </div>

        <div id="eva-pagination">

            <?php
            the_posts_pagination(array(
                'prev_text' => '< <span class="screen-reader-text">' . __('Previous page', 'twentyseventeen') . '</span>',
                'next_text' => '> <span class="screen-reader-text">' . __('Next page', 'twentyseventeen') . '</span>',
                'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'twentyseventeen') . ' </span>',
            ));

            echo "</div>";

        else :

            echo "There are no Evangelus messages to display.";

        endif; ?>
    </main>
</div>

<?php

get_footer();
