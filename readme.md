# DPI Bulletins Plugin

Auto generate links to DPI client bulletins.

```
[bulletins] // Generates links to bulletin pdfs formatted by date.
[bulletin_cover] // generates links to bulletin with a picture of the cover.
```


If you don't want to use the shortcodes you can get the bulletins programmatically.

```php
$controller = Bulletins\Plugin\Controller::getInstance();
$bulletinID = $controller->getBulletinID();
$bulletins = $controller->Transport->getBulletins($bulletinID, 10);

// $bulletins will be an array of arrays where each item represents a bulletin.

Array(
	[07-30-2017.pdf] => Array(
    	'date' => '2017-07-30',
        'links' => Array(
        	'bulletin' => //link to bulletin
            'cover' => //link to cover
        )
    ),
    [07-23-2017.pdf] => Array(
    	'date' => '2017-07-23',
        'links' => Array(
        	'bulletin' => //link to bulletin
            'cover' => //link to cover
        )
    ),
    // etc...
);
```