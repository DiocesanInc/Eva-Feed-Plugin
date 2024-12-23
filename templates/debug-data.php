<?php if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="dpi_eva_feed_wrapper">
	<h5>Evangelus Feed</h5>
   
	<?php echo "URL: " . $args['url']; 
	
	echo $evaPrimary . " - " . $evaSecondary;
   
	$rss_feed = simplexml_load_file( $args['url'] );
	
	global $wpdb;
	$plugin = Eva\Plugin\Controller::getInstance();
	$items = $wpdb->get_results( "SELECT item_url FROM {$plugin->getFeedTable()}", "ARRAY_A" );
	#$imported = array_column($items, 'item_url');
	
	$imported = array();
    foreach ($items as $item) {
		var_dump($item);
        $imported[] = $item['item_url'];
    }
	
	#$imported = array( "https://eva.diocesan.com/message/078abf85ae7937c5d9a45bbe9efec453bad69490","https://eva.diocesan.com/message/17946e51fd7444ff16f8ad13b2e7d589473f3478","https://eva.diocesan.com/message/e05073b51decca56be8b9f6752409dbf8e6f6469","https://eva.diocesan.com/message/7006c4ef80664a27388b10f540f0eac9e200058a","https://eva.diocesan.com/message/02d4b31f9576c638bc7e469ee240f07f0a9b9aa6" );
	
	#var_dump($imported);
	if(!empty($rss_feed)) {
		$i=0;
		foreach ($rss_feed->channel->item as $feed_item) {
			if($i>=10) break;
			#echo $feed_item->link;
		if( ! in_array( (string)$feed_item->link, $imported, true ) ) {
			#echo "Available";
		}
		#echo (string)$feed_item->description;
		#$tables = "/<table.*?<.*\/table>/";
		#preg_match($tables, (string)$feed_item->description, $data );
		#var_dump( $data );
		
		#$content = addslashes( (string)$feed_item->description );
		#$sql = "UPDATE `wp_posts` SET `post_content` = '<![CDATA[ " . $content . " ]]>' WHERE `wp_posts`.`ID` = 857";
		#echo $sql;
		
		/*preg_match_all('/<img.+?src=[\'"]([^\'"]+)[\'"].*?>/i', (string)$feed_item->description, $matches);*/
		#$url = $matches[1][0];
		#echo $url;
		#die;
			
			?>
			<div><a class="feed_title" href="<?php echo $feed_item->link; ?>"><?php echo $feed_item->title; ?></a></div>
			<div><?php #var_dump( $feed_item ); ?></div>
			<div><?php echo $feed_item->image->url; ?>
			<div><?php #echo (string)$feed_item->description; ?></div>
			<div><?php #echo implode(' ', array_slice(explode(' ', $feed_item->description), 0, 14)) . "..."; ?></div>
			
			<div>ID: <?php $s = explode("/",$feed_item->link);
						echo end($s);
						?></div>
			<?php		
			$i++;	
		}
	}
?>
</div>