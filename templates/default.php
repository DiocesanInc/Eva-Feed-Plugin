<?php if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="dpi_bulletin_wrapper">
    <?php if (!empty($bulletins)): ?>
        <?php foreach($bulletins as $bulletin) { ?>
            <?php if ($args['cover_count'] > 0): ?>
                <div class='dpi_bulletin_cover_wrapper'>
                    <span class='dpi_bulletin_cover_date'><?= date("F jS, Y", strtotime($bulletin['date'])) ?></span>
                    <br>
                    <a href="<?= $bulletin['links']['bulletin'] ?>" target="_blank" rel="noopener">
                        <img class='dpi_bulletin_cover' src="<?= $bulletin['links']['cover'] ?>" alt="Bulletin Cover" />
                    </a>
                </div>
                <?php $args['cover_count']-- ?>
            <?php else: ?>
				<?php 
					$label = $args['title'] ? $args['title'] : date("F j, Y", strtotime($bulletin['date'])); 
					if( get_locale() == 'es_ES' && !$args['title'] ) { 
						$label = $Controller->translateDate( $bulletin['date'] );
					}
				?>
                <div class="dpi_bulletin">
                    <a href="<?= $bulletin['links']['bulletin'] ?>" <?= $bulletin['new_tab'] ? " target='_blank'" : "" ?> rel="noopener">
                        <?= $label ?>
                    </a>
                </div>
            <?php endif; ?>
        <?php } ?>
    <?php else: ?>
        <p class="dpi_bulletin_empty">Sorry, there aren't any bulletins to show you right now.</p>
    <?php endif; ?>
</div>