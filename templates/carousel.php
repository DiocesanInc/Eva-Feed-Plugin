<?php if (!defined('ABSPATH')) exit; ?>

<?php if ($evaPrimary || $evaSecondary) { ?>
<style type="text/css">
.dpi_eva_item_title,
.dpi_eva_item_title a,
.dpi_eva_item_read_more,
.dpi_eva_item_read_more a {
    color: <?php echo $evaPrimary;
    ?> !important;
}

evaButton {
    background: <?php echo $evaSecondary;
    ?> !important;
}
</style>
<?php } ?>

<div id="dpi_eva_feed_wrapper">

    <img id="dpi_eva_logo" src="<?php echo plugins_url('../images/eva-logo-horizontal.png', __FILE__); ?>" />

    <?php
    global $post;
    if ($evaPosts) { ?>
    <div id="dpi_eva_feed_carousel">
        <?php
            foreach ($evaPosts as $post) :
                setup_postdata($post);

                include DPI_EVA_FEED_DIR . '/templates/excerpt-eva.php';

            endforeach;

            wp_reset_postdata();
            ?>
    </div>
    <div id="dpi_eva_feed_cta">
        <a href="<?php echo $archiveLink; ?>">
            <evaButton>View More</evaButton>
        </a>
        <?php if ($evaSubscribeLink) { ?>
        <a href="<?php echo $evaSubscribeLink; ?>" target="_blank">
            <evaButton>Subscribe</evaButton>
        </a>
        <?php } ?>
    </div>
    <?php
    } else {
        echo "No Evangelus messages to display.";
    }
    ?>

</div>
