<?php
	require_once '../src/Helpers.php';
	require_once '../src/Options.php';
	require_once '../src/Utils.php';
	require_once '../src/Draw.php';
	require_once '../src/PosterGen.php';
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<style>
			body {
				text-align: center;
				background: #74E868;
			}

			.code {
				max-height: 100px;
				overflow: scroll;
				display: inline-block;
			}

			img { 
				padding: 50px;
				border: 1px solid #14D100;
				margin: 10px;
				background: #CDF76F;
			}
		</style>
	</head>
	<body>
	<?php
			/**
			 * 
			 */
			$poster0 = ( new \PosterGen\PosterGen( [ ] ) )
						->setSize( 80, 80 )
						->setBackgroundColor( '#00BD39' )
						->setBackgroundImage( __DIR__ . "/backgrounds/1.jpg" )
						->setHorizontalAlignment( 'center' )
						->setVerticalAlignment( 'center' )
						->setBorder( 'black', 1 )
						->setOverlayColor( '#025167', 80 )
						->setFont( __DIR__ . "/fonts/Blogger_Sans.otf" )
						//
						->setFontSize( 14 )
						->setFontColor( '#FF5A40' )
						->setFontStroke( '#025167' )
						->addText( 'PosterGen' )
						//
						->setFontSize( 12 )
						->setFontColor( '#FFC640' )
						->addText( 'Poster' )
						->addText( 'generator' );

			echo $poster0->saveToBase64Image( );
		?>
		
		<br />

		<?php
			/**
			 * 
			 */
			$poster1 = ( new \PosterGen\PosterGen( [ ] ) )
						->setSize( 590, 300 )
						->setBackgroundColor( '#00BD39' )
						->setBackgroundImage( __DIR__ . "/backgrounds/1.jpg" )
						->setVerticalAlignment( 'top' )
						->setHorizontalAlignment( 'center' )
						->setVerticalPadding( 30 )
						->setHorizontalPadding( 30 )
						->setBorder( 'black', 1 )
						->setOverlayColor( '#025167', 80 )
						->setFont( __DIR__ . "/fonts/Blogger_Sans.otf" )
						//
						->setFontSize( 40 )
						->setFontColor( '#FF5A40' )
						->setFontStroke( '#025167' )
						->addText( 'PosterGen' )
						//
						->setFontSize( 23 )
						->setFontColor( '#FFC640' )
						->addText( 'Poster generator for all' )
						//
						->addSeparator( '', 'center' )
						//
						->setHorizontalAlignment( 'left' )
						->setVerticalAlignment( 'bottom' )
						->setFontSize( 19 )
						->setFontColor( '#39AECF' )
						->addText( '&#8226; Poster for your site or blog' )	
						->addText( '&#8226; Poster for social network' )
						->addText( '&#8226; Poster for your promo materials &#9787;' )
						//
						->setHorizontalAlignment( 'right' )
						->setTextBackground( '#007B25' )
						->setVerticalAlignment( 'bottom' )
						->setFontSize( 14 )
						->setFontColor( '#FFFFFF' )
						->addText( 'best for you!', '', 0, '', [ ], [ ] );

			echo $poster1->saveToBase64Image( );
		?>
		
		<br />

		<?php
			/**
			 * 
			 */
			$poster2 = ( new \PosterGen\PosterGen( [ ] ) )
						->setSize( 640, 640 )
						->setBackgroundImage( __DIR__ . "/backgrounds/4.jpg" )
						->setHorizontalAlignment( 'center' )
						->setVerticalAlignment( 'center' )
						->setOverlayColor( '#000000' )
						->setBorder( 'black', 1 )
						//
						->setFont( __DIR__ . "/fonts/Ge_Body" )
						->setFontSize( 40 )
						->setFontColor( '#FFFFFF' )
						->addText( 'Overwatch: New Hero Brigitte' )
						->addText( '' )
						//
						->setFontSize( 20 )
						->setFontColor( '#FF5A40' )
						->addText( 'Release Date' )
						//
						->setTextBackground( 'white', 0 )
						->setHorizontalAlignment( 'left' )
						->setVerticalAlignment( 'top' )
						->setFontSize( 32 )
						->setFontColor( 'black' )
						->setFontShadow( '' )
						->setFontStroke( 'black' )
						->addText( 'OVERWATCH' )
						//
						->setTextBackground( '' )
						->setHorizontalAlignment( 'left' )
						->setVerticalAlignment( 'bottom' )
						->setFontSize( 14 )
						->setFontColor( '#FFFFFF' )
						->setFontShadow( '' )
						->setFontStroke( 'black' )
						->addText( 'http://news.com' );

			echo $poster2->saveToBase64Image( );
		?>
			
		<br />

		<?php
			/**
			 * 
			 */
			$poster3 = ( new \PosterGen\PosterGen( [ ] ) )
						->setSize( 860, 300 )
						->setBackgroundImage( __DIR__ . "/backgrounds/3.jpg" )
						->setHorizontalAlignment( 'center' )
						->setVerticalAlignment( 'center' )
						->setOverlayColor( '#000000' )
						->setBorder( 'black', 1 )
						//
						->setFont( __DIR__ . "/fonts/CaviarDreams_Bold" )
						->setFontSize( 40 )
						->setFontColor( '#FFFFFF' )
						->addText( 'Windows 10' )
						//
						->setFontSize( 20 )
						->setFontColor( '#559ce4' )
						->addText( 'Review April 2018 Update' )
						//
						->setTextBackground( 'white', 0 )
						->setVerticalAlignment( 'top' )
						->setFontSize( 24 )
						->setFontColor( 'black' )
						->setFontShadow( '' )
						->setFontStroke( 'black' )
						->addText( 'News > Tech' )
						//
						->setTextBackground( 'black', 0 )
						->setHorizontalAlignment( 'right' )
						->setVerticalAlignment( 'bottom' )
						->setFontSize( 14 )
						->setFontColor( '#FFFFFF' )
						->setFontShadow( '' )
						->setFontStroke( 'black' )
						->addText( 'http://news.com' );

			echo $poster3->saveToBase64Image( );
		?>
		
		<br />
		
		<?php
			/**
			 * 
			 */
			$poster4 = ( new \PosterGen\PosterGen( [ ] ) )
						->setSize( 350, 200 )
						->setBackgroundColor( '#FFFFFF' )
						->setFontColor( '#9A0000' )
						->setFontSize( 26 )
						->setFontStroke( '#FFFFFF', 2 )
						->setBorder( '#9A0000', 2 )
						->setFont( __DIR__ . "/fonts/ComicRelief" )
						//
						->addImage( __DIR__ . "/images/clipboard.png", [ 'inline' => true ] )
						//
						->setVerticalAlignment( 'top' )
						->setHorizontalAlignment( 'left' )
						->addText( 'Documentation' )
						//
						->setVerticalAlignment( 'center' )
						->setHorizontalAlignment( 'center' )
						->addText( 'Instruction', '', 34 )
						//
						->setVerticalAlignment( 'bottom' )
						->setHorizontalAlignment( 'right' )
						//
						->addText( 'F.A.Q', '', 36 );

			echo $poster4->saveToBase64Image( );
		?>

		<?php
			/**
			 * 
			 */
			$poster5 = ( new \PosterGen\PosterGen( [ ] ) )
						->setSize( 350, 200 )
						->setBackgroundColor( '#FF5A40' )
						->setFontColor( '#FFC640' )
						->setFontSize( 24 )
						->setFontStroke( '#007B25' )
						->setFont( __DIR__ . "/fonts/ComicRelief" )
						->setVerticalAlignment( 'top' )
						//
						->addImage( __DIR__ . "/images/wink.png" )
						//
						->setVerticalAlignment( 'bottom' )
						->addText( 'Smiles improve health' );

			echo $poster5->saveToBase64Image( );
		?>

		<?php
			/**
			 * 
			 */
			$poster6 = ( new \PosterGen\PosterGen( [ ] ) )
						->setSize( 350, 200 )
						->setBackgroundGradient( [ '#FF5A40', '#007B25', '#FFC640', '#007B25' ] )
						->setFontColor( '#FFFFFF' )
						->setFontSize( 24 )
						->setFontStroke( '#007B25' )
						->setFont( __DIR__ . "/fonts/ComicRelief" )
						->addText( "Party on\r\nLasVegas" );

			echo $poster6->saveToBase64Image( );
		?>
	</body>
</html>
