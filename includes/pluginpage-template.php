<?php if ( !defined( 'ABSPATH' ) ) exit; ?>

<div id="<?= $this->pageSlug ?>" class="wrap">
    <?= settings_errors( null, null, true ) ?>
    <h1><?= $this->pageTitle ?></h1>
    <form method="post" action="options.php" enctype="multipart/form-data">
        <?= do_settings_sections($this->pageSlug) ?>
        <?= submit_button() ?>
    </form>
    <hr>
    <h3>Shortcodes</h3>
    <p>Eva Feed Shortcode: [eva_feed]</p>
</div>