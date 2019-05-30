<?php
namespace PosterGen;

trait Draw
{
	/**
	 * 
	 */
	protected function strokeText( $image, $values, $position, $color, $size )
	{
		if( $size <= 0 || $color === '' ) { return $this; }

		$x = $position[ 'x' ]; 
		$y = $position[ 'y' ];
		
		for( $c1 = $x - $size; $c1 <= $x + $size; $c1++ ) 
		{
			for( $c2 = $y - $size; $c2 <= $y + $size; $c2++ ) 
			{
				$this->drawInternal( $image, $values, [ 'x' => $c1, 'y' => $c2 ], $color );
			}
		}

		return $this;
	}

	/**
	 * @todo: https://stackoverflow.com/questions/14741622/underline-text-using-imagettftext
	 */
	protected function drawLine( $image, $position, $color )
	{
		$colorAllocate = $this->createColor( $image, $color );

		imageLine( $image, 
					$position[ 'x1' ], 
					$position[ 'y1' ], 
					$position[ 'x2' ], 
					$position[ 'y2' ], 
					$colorAllocate );

		return $this;
	}

	/**
	 * 
	 */
	protected function drawBorder( $image, $color, $size )
	{
		$colorAllocate = $this->createColor( $image, $color );

		for( $i = 0; $i < $size; $i++) 
		{
			imageRectangle( $image, 
							0 + $i, 
							0 + $i, 
							$this->size[ 'width' ] - ( 1 + $i ), 
							$this->size[ 'height' ] - ( 1 + $i ), 
							$colorAllocate );
		}

		return $this;
	}
	
	/**
	 * 
	 */
	protected function drawInternal( $image, $values, $position, $color, $transparent = 0 )
	{
		$colorAllocate = $this->createColor( $image, $color, $transparent );

		imageTTFText( $image, 
					$values[ 'font-size' ], 
					$values[ 'angle' ], 
					$position[ 'x' ], 
					$position[ 'y' ], 
					$colorAllocate,
					$values[ 'font' ], 
					$values[ 'text' ] );

		return $this;
	}

	/**
	 * 
	 */
	protected function drawBackgroundColor( $image, $position, $color, $transparent, $angle )
	{
		/* @todo: !! */
		if( $angle )
		{
			return $this;
		}

		$padding = 5;
		$colorAllocate = $this->createColor( $image, $color, $transparent );
		$transparencyColor = imageColorAllocateAlpha( $image , 0, 0, 0, 127 );

		// $this->imageFilledRotatedRectangle( $image, $position[ 'x' ], $position[ 'y' ], $position[ 'width' ], $position[ 'height' ], $angle, $colorAllocate );
		imageFilledRectangle( $image, 
							$position[ 'x' ] - $padding, 
							$position[ 'y' ] + $padding, 
							$position[ 'x' ] + $position[ 'width' ] + $padding,
							$position[ 'y' ] - $position[ 'height' ] - $padding, 
							$colorAllocate );
	}

	/**
	 * 
	 */
	protected function drawBackgroundImage( $image, $backgroundFile )
	{
		$backgroundImage = imageCreateFromString( file_get_contents( $backgroundFile ) );
		imageLayerEffect( $backgroundImage, IMG_EFFECT_NORMAL );
		imageSaveAlpha( $backgroundImage, true );

		//
		list( $backgroundImageWidth,  $backgroundImageHeight ) = getImageSize( $backgroundFile );
		$backgroundImageNewWidth = $backgroundImageWidth;
		$backgroundImageNewHeight = $backgroundImageHeight;	

		//
		if( $backgroundImageWidth < $this->size[ 'width' ]  )
		{
			$scalingFactor = $this->size[ 'width' ] / $backgroundImageWidth;
			$backgroundImageNewWidth = $this->size[ 'width' ];
			$backgroundImageNewHeight = round( $backgroundImageHeight * $scalingFactor );
		}
		// 
		else if( $backgroundImageHeight < $this->size[ 'height' ] )
		{
			$scalingFactor = $this->size[ 'height' ] / $backgroundImageWidth;
			$backgroundImageNewHeight = $this->size[ 'height' ];
			$backgroundImageNewWidth = round( $backgroundImageHeight * $scalingFactor );
		}

		//
		imageCopyResampled( $image, $backgroundImage, 
							0, 0, 
							0, 0,
							$backgroundImageNewWidth, $backgroundImageNewHeight, 
							$backgroundImageWidth, $backgroundImageHeight );

		//
		imageDestroy( $backgroundImage );
	}

	/**
	 * 
	 */
	protected function drawOverlay( $image, $color, $transparent )
	{
		$colorAllocate = $this->createColor( $image, $color, $transparent );
		$padding = 3;

		imageFilledRectangle( $image, 0, 0, $this->size[ 'width' ], $this->size[ 'height' ], $colorAllocate );

		return $this;	
	}

	/**
	 * 
	 */
	function generate( /*string*/ $format = 'png', $fileName = null )
	{
		//
		$image = imageCreateTrueColor( $this->size[ 'width' ], $this->size[ 'height' ] );
		imageLayerEffect( $image, IMG_EFFECT_NORMAL );
		imageSaveAlpha( $image, true );

		// Check for PHP < 7.2
		if( function_exists( 'imageAntialias' ) ) { imageAntialias( $image, True ); }

		// Colors
		$bgColor = $this->createColor( $image, $this->backgroundColor );

		//
		imageFillToBorder( $image, 0, 0, $bgColor, $bgColor );

		// Background Gradient
		if( is_array( $this->backgroundGradient ) && count( $this->backgroundGradient ) > 0 )
		{
			$this->imageFillGradient( $image, $this->backgroundGradient );
		}

		// Background Image
		if( !empty( $this->backgroundImage ) )
		{
			$this->drawBackgroundImage( $image, $this->backgroundImage );
		}

		// Overlay
		if( !empty( $this->overlayColor ) )
		{
			$this->drawOverlay( $image, $this->overlayColor, $this->overlayTransparent );
		}

		// Расчитываем высоту блоков
		$sectionsHeight = $this->calculateSectionsHeight( );

		//
		$verticalPadding = $this->verticalPadding + $this->borderSize;
		$horizontalPadding = $this->horizontalPadding + $this->borderSize;

		//
		$currentBottom = [ 
			'top' => $verticalPadding,
			'bottom' => $this->size[ 'height' ] - $sectionsHeight[ 'bottom' ] - $verticalPadding,
			'center' => ( ( $this->size[ 'height' ] - $sectionsHeight[ 'center' ] ) / 2.0 ) - $verticalPadding
		];

		// Debug
		if( $this->debug )
		{
			$this->paintDebugData( $image, $sectionsHeight, $currentBottom );
		}

		//
		$objectIndex = -1;

		// Start position
		foreach( $this->objectList as $value )
		{
			$objectIndex++;

			//
			$horizontalAlignment = array_get( $value, 'position.horizontal-alignment' );
			$verticalAlignment = array_get( $value, 'position.vertical-alignment' );

			// Horizontal position
			if( !empty( array_get( $value, 'position.x' ) ) ) { $left = array_get( $value, 'position.x' ); }
			else 
			{
				if( $horizontalAlignment === 'center' ) { $left = ( $this->size[ 'width' ] - array_get( $value, 'size.width' ) ) / 2.0; }
				else if( $horizontalAlignment === 'right' ) { $left = ( $this->size[ 'width' ] - array_get( $value, 'size.width' ) ) - $horizontalPadding; }
				else { $left = $horizontalPadding; }
			}

			// Image
			if( $value[ 'type' ] === 'image' )
			{
				// Vertical position
				if( !empty( array_get( $value, 'position.y' ) ) ) { $top = array_get( $value, 'position.y' ); }
				else
				{
					$top = $currentBottom[ $verticalAlignment ] - $this->lineSpacing;
					if( array_get( $value, 'position.vertical-alignment' ) === 'center' ) { $top += ( $this->size[ 'height' ] - array_get( $value, 'size.height' ) ) / 2.0; }
					else if( array_get( $value, 'position.vertical-alignment' ) === 'bottom' ) { $top += ( $this->size[ 'height' ] - array_get( $value, 'size.height' ) ) - $verticalPadding; }
					else { $top += $verticalPadding; }

					if( !$value[ 'inline' ] )
					{
						$currentBottom[ $verticalAlignment ] = $top + $this->lineSpacing;
					}
				}

				//
				$customImage = imageCreateFromString( file_get_contents( $value[ 'image' ] ) );
				imageLayerEffect( $customImage, IMG_EFFECT_NORMAL );
				imageSaveAlpha( $customImage, true );

				//
				$width = array_get( $value, 'size.width' );
				$height = array_get( $value, 'size.height' );

				//
				$customTop = ( ( $top + $height ) > $this->size[ 'height' ] ) ? $top + ( $this->size[ 'height' ] - ( $top + $height ) ) : $top;
				$customLeft = ( ( $left + $width ) > $this->size[ 'width' ] ) ? $left + ( $this->size[ 'width' ] - ( $left + $width ) ) : $left;

				// Copy image to canvas
				imageCopyResampled( $image,
									$customImage, 
									$customLeft, $customTop, 
									0, 0,
									$width, $height,
									array_get( $value, 'size.original-width' ), array_get( $value, 'size.original-height' ) );

				// 
				imageDestroy( $customImage );
			}
			// Text
			else if( $value[ 'type' ] === 'text' )
			{
				$charHeightDiffer = array_get( $value, 'coordinate.charHeightDiffer' );

				// Vertical position
				if( !empty( array_get( $value, 'position.y' ) ) ) { $top = array_get( $value, 'position.y' ); }
				else
				{
					$lineSpacing = $this->lineSpacing; 
					// $lineSpacing -= ( mb_strtolower( $value[ 'text' ] ) === $value[ 'text' ] ) ? $charHeightDiffer : 0;

					//
					$top = $currentBottom[ $verticalAlignment ];
					$top += ( $objectIndex === 0 ? 0 : $lineSpacing ) + array_get( $value, 'size.height' );

					//
					$currentBottom[ $verticalAlignment ] = $top;
				}

				//
				$stroke = $value[ 'stroke' ];
				$shadow = $value[ 'shadow' ];

				//
				if( empty( $value[ 'text' ] ) ) 
				{
					continue;
				}

				//
				if( $value[ 'text' ] === '-' )
				{
					$separatorWidth = $this->size[ 'width' ] / 2;
					// $separatorLeft = ( $this->size[ 'width' ] - $separatorWidth ) / 2;

					if( $horizontalAlignment === 'center' ) { $separatorLeft = ( $this->size[ 'width' ] - $separatorWidth ) / 2; }
					else if( $horizontalAlignment === 'right' ) { $separatorLeft = ( $this->size[ 'width' ] - $separatorWidth ); }
					else { $separatorLeft = $this->horizontalPadding + $this->borderSize; }

					$this->drawLine( $image, 
									[
										'x1' => $separatorLeft, 
										'x2' => $separatorLeft + $separatorWidth, 
										'y1' => $top - array_get( $value, 'coordinate.y' ) - ( array_get( $value, 'size.height' ) / 2 ),
										'y2' => $top - array_get( $value, 'coordinate.y' ) - ( array_get( $value, 'size.height' ) / 2 ),
									],
									$value[ 'color' ] );

					continue;
				}
			
				// Underline text
				if( is_array( $value[ 'style' ] ) && in_array( 'underline', $value[ 'style' ] ) )
				{
					$this->drawLine( $image, 
									[
										'x1' => $left, 
										'x2' => $left + array_get( $value, 'size.width' ), 
										'y1' => $top - array_get( $value, 'coordinate.y' ) + 2, 
										'y2' => $top - array_get( $value, 'coordinate.y' ) + 2
									],
									$value[ 'color' ] );
				}

				// Stroke text
				if( is_array( $stroke ) && count( $stroke ) > 0  )
				{
					$this->strokeText( $image, 
										$value, 
										[ 
											'x' => $left, 
											'y' => $top 
										], 
										$stroke[ 'color' ], 
										$stroke[ 'size' ] );
				}

				// Shadow
				if( is_array( $shadow ) && count( $shadow ) > 0 )
				{
					$this->drawInternal( $image, 
										$value,
										[ 
											'x' => $left + array_get( $shadow, 'offset.x', 0 ), 
											'y' => $top + array_get( $shadow, 'offset.y', 0 ) 
										], 
										$shadow[ 'color' ] );
				}

				// Text-line Background
				if( is_array( $value[ 'background' ] ) && !empty( array_get( $value, 'background.color' ) ) )
				{
					$this->drawBackgroundColor( $image, 
											[ 
												'x'	=> $left,
												'x1' => $left, 
												'x2' => $left + array_get( $value, 'size.width' ), 
												'y' => $top, 
												'y1' => $top - array_get( $value, 'coordinate.y' ), 
												'y2' => $top - array_get( $value, 'size.height' ),
												'height' => array_get( $value, 'size.height' ),
												'width' => array_get( $value, 'size.width' ) 
											], 
											array_get( $value, 'background.color' ), 
											array_get( $value, 'background.transparent' ),
											$value[ 'angle' ] );
				}

				// Draw text
				$this->drawInternal( $image,
									$value, 
									[ 
										'x' => $left, 
										'y' => $top
									], 
									$value[ 'color' ] );

				// Strike text
				if( is_array( $value[ 'style' ] ) && in_array( 'strike', $value[ 'style' ] ) )
				{
					$this->drawLine( $image,
									[
										'x1' => $left,
										'x2' => $left + array_get( $value, 'size.width' ),
										'y1' => $top - array_get( $value, 'coordinate.y' ) - ( array_get( $value, 'size.height' ) / 2 ),
										'y2' => $top - array_get( $value, 'coordinate.y' ) - ( array_get( $value, 'size.height' ) / 2 ),
									],
									$value[ 'color' ] );
				}
			}
		}

		// Border
		if( !empty( $this->borderColor ) && $this->borderSize > 0 )
		{
			$this->drawBorder( $image, $this->borderColor, $this->borderSize );
		}

		// 
		if( $format === 'jpeg' || $format === 'jpg' ) { imageJPEG( $image, $fileName ); }
		else if( $format === 'png' ) { imagePNG( $image, $fileName, 6, PNG_NO_FILTER  ); }
		else if( $format === 'gif' ) { imageGIF( $image, $fileName ); }

		//
		imageDestroy( $image );
	}
}