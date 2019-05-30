## PosterGen - News and Articles poster generator

Use for:
* Poster for your site or blog
* Poster for social network
* Poster for your promo materials &#9787;

## Example Images
![PosterGen](https://github.com/ange007/PosterGen/blob/master/icon.png) 

![PosterGen](https://github.com/ange007/PosterGen/blob/master/poster.png)

![PosterGen](https://github.com/ange007/PosterGen/blob/master/examples/1.png)

![PosterGen](https://github.com/ange007/PosterGen/blob/master/examples/2.png)

![PosterGen](https://github.com/ange007/PosterGen/blob/master/examples/3.png)

![PosterGen](https://github.com/ange007/PosterGen/blob/master/examples/4.png)

![PosterGen](https://github.com/ange007/PosterGen/blob/master/examples/5.png)

## Dependencies
* PHP >= 5.6
* GD

## Install
```
composer require ange007/poster-gen
```

## Example Code
```php
<?php
	use \PosterGen;
	
	// Generate poster
	$poster = ( new \PosterGen\PosterGen( [ ] ) )
		->setSize( 1280, 720 )
		->setBackgroundImage( __DIR__ . "/backgrounds/1.jpg" )
		->setHorizontalAlignment( 'center' )
		->setVerticalAlignment( 'center' )
		->setFontShadow( '#333333', -2, 2 )
		->setOverlayColor( '#FF0000' )
		->setBorder( 'black', 1 )
		// Title
		->setFont( __DIR__ . "/fonts/Roboto-Regular" )
		->setFontSize( 40 )
		->setFontColor( '#FFFFFF' )
		->addText( 'Microsoft buying GitHub' )
		->addText( '' )
		// Subtitle
		->setFont( __DIR__ . "/fonts/Blogger_Sans.otf" )
		->setFontSize( 20 )
		->setFontColor( '#00FFFF' )
		->addText( 'The deal is concluded' )
		// Watermark
		->setTextBackground( 'black', 50 )
		->setHorizontalAlignment( 'right' )
		->setVerticalAlignment( 'bottom' )
		->setFontSize( 14 )
		->setFontColor( '#FFFFFF' )
		->setFontShadow( '' )
		->setFontStroke( 'black' )
		->addText( 'http://news.com' );
		  
	// Poster output
	echo $poster->saveToBase64Image( );
```

## Plans
* Background position and adaptation
* Text rotate
* Image rotate

## License
It is released under the [MIT License](LICENSE).
