<?php
namespace PosterGen;

trait Utils
{
	/**
	 * 
	 */
	protected function calculateTextCoordinates( $text, $size, $position, $color, $angle, $font )
	{
		// 
		$bbox = $this->imageTTFBBoxExtended( $size, $angle, $font, $text );

		//
		$customWidth = $bbox[ 'width' ]; /*abs( $bbox[ 0 ] - $bbox[ 4 ] );*/
		$customHeight = $bbox[ 'height' ]; /*abs( $bbox[ 1 ] - $bbox[ 5 ] );*/

		//
		$x = $bbox[ 'x' ] + ( ( is_array( $position ) && !empty( $position[ 'x' ] ) ) ? $position[ 'x' ] : 0 );
		$y = $bbox[ 'y' ] + ( ( is_array( $position ) && !empty( $position[ 'y' ] ) ) ? $position[ 'y' ] : 0 );

		// Clear memory
		$bbox = null;

		return [
			'width'		=> $customWidth,
			'height'	=> $customHeight,
			'x'			=> $y, // ?
			'top'		=> $y,
			'bottom'	=> $y + $customHeight,
			'y'			=> $x, // ?
			'left'		=> $x,
			'right'		=> $x + $customWidth,
		];
	}

	/**
	 * https://stackoverflow.com/questions/36929656/imagettfbbox-calculates-wrong-rectangle-when-text-begins-with-number#comment61698419_36929656
	 */
	protected function imageTTFBBoxExtended( $size, $angle, $fontFile, $text ) 
	{
		// Minimal height calculation
		$patternBBox = imageTTFBBox( $size, $angle, $fontFile, 'AMWpq' );

		/*this function extends imagettfbbox and includes within the returned array
		the actual text width and height as well as the x and y coordinates the
		text should be drawn from to render correctly.  This currently only works
		for an angle of zero and corrects the issue of hanging letters e.g. jpqg*/
		$bbox = imageTTFBBox( $size, $angle, $fontFile, $text );
	
		// calculate x baseline
		if( $bbox[ 0 ] >= -1 ) { $bbox[ 'x' ] = abs( $bbox[ 0 ] + 1 ) * -1; } 
		else { $bbox[ 'x' ] = abs( $bbox[ 0 ] + 2 ); }
	
		// calculate actual text width
		if( $bbox[ 0 ] < -1 ) {	$bbox[ 'width' ] = abs( $bbox[ 2 ] ) + abs( $bbox[ 0 ] ) - 1; }
		else { $bbox[ 'width' ] = abs( $bbox[ 2 ] - $bbox[ 0 ] ); }
	
		// calculate y baseline
		$bbox[ 'y' ] = abs( $patternBBox[ 5 ] + 1 );
	
		// calculate actual text height
		if( $patternBBox[ 3 ] > 0) { $bbox[ 'height' ] = abs( $patternBBox[ 7 ] - $patternBBox[ 1 ] ) - 1; }
		else { $bbox[ 'height' ] = abs( $patternBBox[ 7 ] ) - abs( $patternBBox[ 1 ] ); }
	
		return $bbox;
	}

	/**
	 * 
	 */
	protected function imageFilledRotatedRectangle( $image, $X, $Y, $width, $height, $angle, $color )
	{
		// First calculate $x1 and $y1. You may want to apply
		// round() to the results of the calculations.
		$x1 = ( -$width * cos( $angle ) / 2 ) + $X;
		$y1 = ( -$height * sin( $angle ) / 2 ) + $Y;

		$x2 = $x1;
		$y2 = ( -$height * sin( $angle ) / 2 ) + $Y;

		$x3 = ( -$width * cos( $angle ) / 2 ) + $X;
		$y3 = ( -$height * sin( $angle ) / 2 ) + $Y;

		$x4 = ( -$width * cos( $angle ) / 2 ) + $X;
		$y4 = ( -$height * sin( $angle ) / 2 ) + $Y;


		// Then calculate $x2, $y2, $x4 and $y4 using similar formulae. (Not shown)
		// To calculate $x3 and $y3, you can use similar formulae again, *or*
		// if you are using round() to obtain integer points, you should probably
		// calculate the vectors ($x1, $y1) -> ($x2, $y2) and ($x1, $y1) -> ($x3, $y3)
		// and add them both to ($x1, $y1) (so that you do not occasionally obtain
		// a wonky rectangle as a result of rounding error). (Not shown)
		// imageFilledPolygon( $image, [ $x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4 ], 4, $color );

		// Рисование многоугольника
		/*
		imageFilledPolygon($image, [
			$X, $Y - $height,
			$X, $Y + $height,
			$X + $width, $Y + $height,
			$X + $width, $Y - $height
		],
		4,
		$color );
		*/

		// Top-Left
		$x1 = $X - ( $width * cos( $angle ) / 2 );
		$y1 = $Y - ( $height * sin( $angle ) );

		// Bottom-Left
		$x2 = $x1 + 10;
		$y2 = ( -$height * sin( $angle ) / 2 ) + $Y;

		// Top-Right
		$x3 = ( -$width * cos( $angle ) / 2 ) + $X;
		$y3 = ( -$height * sin( $angle ) / 2 ) + $Y;

		// Bottom-Right
		$x4 = ( -$width * cos( $angle ) / 2 ) + $X;
		$y4 = ( -$height * sin( $angle ) / 2 ) + $Y;	

		imageFilledPolygon( $image, [
			$x1, $y1,
			$x2, $y2,
			$x3, $y3,
			$x4, $y4,
		],
		2,
		$color );

		return $image;
	}

	/**
	 * 
	 */
	protected function imageFillGradient( $image, $colors = [ ] )
	{
		$width = imagesX( $image );
		$height = imagesY( $image );
	
		// Convert hex-values to rgb
		for( $i = 0; $i <= 3; $i++ )
		{
			$colors[ $i ] = [
				hexdec( substr( $colors[ $i ], 1, 2 ) ),
				hexdec( substr( $colors[ $i ], 3, 2 ) ),
				hexdec( substr( $colors[ $i ], 5, 2 ) )
			];
		}

		// Start with top left color
		$rgb = $colors[ 0 ]; 

		for( $x = 0; $x <= $width; $x++ ) 
		{
			for( $y = 0 ; $y <= $height; $y++ ) 
			{
				// set pixel color 
				$col = imageColorAllocate( $image, $rgb[0], $rgb[1], $rgb[2] );
				imageSetPixel( $image, $x - 1, $y - 1, $col );

				// calculate new color  
				for( $i = 0; $i <= 2; $i++ ) 
				{
					$rgb[ $i ] =
						$colors[ 0 ][ $i ] * ( ( $width - $x ) * ( $height - $y ) / ( $width * $height ) ) +
						$colors[ 1 ][ $i ] * ( $x * ( $height - $y ) / ( $width * $height ) ) +
						$colors[ 2 ][ $i ] * ( ( $width - $x ) * $y  / ( $width * $height ) ) +
						$colors[ 3 ][ $i ] * ( $x * $y / ( $width * $height ) );
				}
			}
		}

		return $image;
	}

	/**
	 * 
	 */
	protected function calculateSectionsHeight( )
	{
		$sectionsHeight = [ 
			'all' => 0,
			'top' => 0,
			'bottom' => 0,
			'center' => 0 
		];
		
		foreach( $this->objectList as $value ) 
		{
			if( array_get( $value, 'inline', false ) )
			{ 
				continue;
			}

			$height = $value[ 'size' ][ 'height' ] + $this->linePadding;

			$sectionsHeight[ 'all' ] += $height;
			$sectionsHeight[ 'top' ] += ( empty( $value[ 'position' ][ 'y' ] ) && $value[ 'position' ][ 'vertical-alignment' ] === 'top' ) ? $height : 0;
			$sectionsHeight[ 'bottom' ] += ( empty( $value[ 'position' ][ 'y' ] ) && $value[ 'position' ][ 'vertical-alignment' ] === 'bottom' ) ? $height : 0;
			$sectionsHeight[ 'center' ] += ( empty( $value[ 'position' ][ 'y' ] ) && $value[ 'position' ][ 'vertical-alignment' ] === 'center' ) ? $height : 0;
		}

		return $sectionsHeight;
	}

	/**
	 * 
	 */
	protected function calculateImageSize( $image )
	{
		$canvasSize = $this->getSize( );
		$width = imagesX( $image );
		$height = imagesY( $image );

		$highSide = ( $canvasSize[ 'width' ] > $canvasSize[ 'height' ] ) ? 'w' : 'h';
		$imageHighSide = ( $width > $height ) ? 'w' : 'h';
		
		if( $imageHighSide == 'h' && ( $height > $canvasSize[ 'height' ] ) )
		{
			$customHeight = $canvasSize[ 'height' ];
			$customWidth = $width * ( $customHeight / $height );
		}
		else if( $imageHighSide == 'w' && ( $width > $canvasSize[ 'width' ] ) )
		{
			$customWidth = $canvasSize[ 'width' ];
			$customHeight = $height * ( $customWidth / $width );
		}
		else
		{
			$customWidth = $width;
			$customHeight = $height;
		}

		//
		return [
			'original-height'	=> $height,
			'original-width'	=> $width,
			'height' 			=> $customHeight,
			'width' 			=> $customWidth
		];
	}

	/**
	 * 
	 */
	protected function createColor( $image, $color, $transparent = 0 )
	{
		$defaultColors = [ 
			'black'		=> '#000000',
			'blue'		=> '#0000FF',
			'brown' 	=> '#A52A2A',
			'cream' 	=> '#FFFFCC',
			'green' 	=> '#008000',
			'grey' 		=> '#808080',
			'yellow' 	=> '#FFFF00',
			'orange' 	=> '#FFA500',
			'pink' 		=> '#FFC0CB',
			'red' 		=> '#FF0000',
			'purple' 	=> '#800080',
			'tan'		=> '#d2b48c',
			'turquoise' => '#40E0D0',
			'white'		=> '#FFFFFF',
			'maroon'	=> '800000',
			'cyan'		=> '00FFFF',
		];

		if( array_key_exists( strtolower( $color ), $defaultColors ) ) { $allocateColor = sscanf( $defaultColors[ $color ], '#%2x%2x%2x' ); }
		else { $allocateColor = sscanf( $color, '#%2x%2x%2x' ); }

		return imageColorAllocateAlpha( $image, $allocateColor[ 0 ], $allocateColor[ 1 ], $allocateColor[ 2 ], $transparent );
	}

	/**
	 * 
	 */
	protected function paintDebugData( $image, $sectionsHeight, $startPosition = [ ] )
	{
		// Vertical center line
		$verticalCenter = $this->size[ 'height' ] / 2.0;
		imageLine( $image, 0, $verticalCenter, $this->size[ 'width' ], $verticalCenter, null );

		// Horizontal center line
		$horizontalCenter = $this->size[ 'width' ] / 2.0;
		imageLine( $image, $horizontalCenter , 0, $horizontalCenter , $this->size[ 'height' ], null );

		// Top section
		if( $sectionsHeight[ 'top' ] > 0 )
		{
			imageRectangle( $image, 
							$this->horizontalPadding, 
							$this->verticalPadding, 
							$this->size[ 'width' ] - $this->horizontalPadding, 
							$this->verticalPadding + $sectionsHeight[ 'top' ], 
							$this->createColor( $image, '#FFEA73' ) );
		}

		// Center section
		if( $sectionsHeight[ 'center' ] > 0 )
		{
			$centerTop = ( $this->size[ 'height' ] - $sectionsHeight[ 'center' ] ) / 2.0;

			imageRectangle( $image, 
							$this->horizontalPadding, 
							$centerTop, 
							$this->size[ 'width' ] - $this->horizontalPadding, 
							$centerTop + $sectionsHeight[ 'center' ] - $this->linePadding, 
							$this->createColor( $image, '#A60000' ) );
		}

		// Bottom section
		if( $sectionsHeight[ 'bottom' ] > 0 )
		{
			imageRectangle( $image, 
							$this->horizontalPadding, 
							$this->size[ 'height' ] - $sectionsHeight[ 'bottom' ] - $this->verticalPadding, 
							$this->size[ 'width' ] - $this->horizontalPadding, 
							$this->size[ 'height' ] - $this->verticalPadding, 
							$this->createColor( $image, '#00CC00' ) );
		}
	}
}