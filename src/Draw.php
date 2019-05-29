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
	protected function drawBackground( $image, $position, $color, $transparent, $angle )
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
		imageFilledRectangle( $image, $position[ 'x' ] - $padding, $position[ 'y' ] + $padding, $position[ 'x' ] + $position[ 'width' ] + $padding, $position[ 'y' ] - $position[ 'height' ] - $padding, $colorAllocate );
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

		// Check for PHP < 7.2
		if( function_exists( 'imageAntialias' )  ) { imageAntialias( $image, True ); }

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
			$backgroundImage = imageCreateFromString( file_get_contents( $this->backgroundImage ) );

			imageCopyMerge( $image, $backgroundImage, 0, 0, 0, 0, $this->size[ 'width' ], $this->size[ 'height' ], $this->backgroundTransparent );
		}

		// Overlay
		if( !empty( $this->overlayColor ) )
		{
			$this->drawOverlay( $image, $this->overlayColor, $this->overlayTransparent );
		}

		// Расчитываем высоту блоков
		$sectionsHeight = $this->calculateSectionsHeight( );

		//
		$currentBottom = [ 
			'top' => $this->verticalPadding,
			'bottom' => $this->size[ 'height' ] - $sectionsHeight[ 'bottom' ] - $this->verticalPadding,
			'center' => ( ( $this->size[ 'height' ] - $sectionsHeight[ 'center' ] ) / 2.0 )
		];

		// Debug
		if( $this->debug )
		{
			$this->paintDebugData( $image, $sectionsHeight, $currentBottom );
		}

		// Start position
		foreach( $this->objectList as $value )
		{
			// 
			$left = $this->horizontalPadding;

			// 
			$horizontalAlignment = $value[ 'position' ][ 'horizontal-alignment' ];
			$verticalAlignment = $value[ 'position' ][ 'vertical-alignment' ];

			// Horizontal position
			if( !empty( $value[ 'position' ][ 'x' ] ) ) { $left = $value[ 'position' ][ 'x' ]; }
			else 
			{
				if( $horizontalAlignment === 'center' ) { $left = ( $this->size[ 'width' ] - $value[ 'size' ][ 'width' ] ) / 2.0; }
				else if( $horizontalAlignment === 'right' ) { $left = ( $this->size[ 'width' ] - $value[ 'size' ][ 'width' ] ) - $this->horizontalPadding; }
				else { $left = $this->horizontalPadding; }
			}

			// Image
			if( $value[ 'type' ] === 'image' )
			{
				// Vertical position
				if( !empty( $value[ 'position' ][ 'y' ] ) ) { $top = $value[ 'position' ][ 'y' ]; }
				else
				{
					$top = $currentBottom[ $verticalAlignment ] - $this->linePadding;
					if( $value[ 'position' ][ 'vertical-alignment' ] === 'center' ) { $top += ( $this->size[ 'height' ] - $value[ 'size' ][ 'height' ] ) / 2.0; }
					else if( $value[ 'position' ][ 'vertical-alignment' ] === 'bottom' ) { $top += ( $this->size[ 'height' ] - $value[ 'size' ][ 'height' ] ) - $this->verticalPadding; }
					else { $top += $this->verticalPadding; }

					if( !$value[ 'inline' ] )
					{
						$currentBottom[ $verticalAlignment ] = $top + $this->linePadding;
					}
				}

				//
				$customImage = imageCreateFromString( file_get_contents( $value[ 'image' ] ) );
				imageLayerEffect( $customImage, IMG_EFFECT_NORMAL );
				imageSaveAlpha( $customImage, true );

				//
				$width = $value[ 'size' ][ 'width' ];
				$height = $value[ 'size' ][ 'height' ];

				//
				$customTop = ( ( $top + $height ) > $this->size[ 'height' ] ) ? $top + ( $this->size[ 'height' ] - ( $top + $height ) ) : $top;
				$customLeft = ( ( $left + $width ) > $this->size[ 'width' ] ) ? $left + ( $this->size[ 'width' ] - ( $left + $width ) ) : $left;

				// Copy image to canvas
				imageCopyResampled( $image,
									$customImage, 
									$customLeft, $customTop, 
									0, 0,
									$width, $height,
									$value[ 'size' ][ 'original-width' ], $value[ 'size' ][ 'original-height' ] );
			}
			// Text
			else if( $value[ 'type' ] === 'text' )
			{
				// Vertical position
				if( !empty( $value[ 'position' ][ 'y' ] ) ) { $top = $value[ 'position' ][ 'y' ]; }
				else
				{
					$top = $currentBottom[ $verticalAlignment ] + $value[ 'size' ][ 'height' ];

					//
					$currentBottom[ $verticalAlignment ] = $top + $this->linePadding;
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
					else if( $horizontalAlignment === 'right' ) { $separatorLeft = ( $this->size[ 'width' ] - $separatorWidth ) - $this->horizontalPadding; }
					else { $separatorLeft = $this->horizontalPadding; }

					$this->drawLine( $image, 
									[
										'x1' => $separatorLeft, 
										'x2' => $separatorLeft + $separatorWidth, 
										'y1' => $top - $value[ 'coordinate' ][ 'y' ] - ( $value[ 'size' ][ 'height' ] / 2 ),
										'y2' => $top - $value[ 'coordinate' ][ 'y' ] - ( $value[ 'size' ][ 'height' ] / 2 ),
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
										'x2' => $left + $value[ 'size' ][ 'width' ], 
										'y1' => $top - $value[ 'coordinate' ][ 'y' ] + 2, 
										'y2' => $top - $value[ 'coordinate' ][ 'y' ] + 2
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

				// Background
				if( is_array( $value[ 'background' ] ) && !empty( $value[ 'background' ][ 'color' ] ) )
				{
					$this->drawBackground( $image, 
											[ 
												'x'	=> $left,
												'x1' => $left, 
												'x2' => $left + $value[ 'size' ][ 'width' ], 
												'y' => $top, 
												'y1' => $top - $value[ 'coordinate' ][ 'y' ], 
												'y2' => $top - $value[ 'size' ][ 'height' ],
												'height' => $value[ 'size' ][ 'height' ],
												'width' => $value[ 'size' ][ 'width' ] 
											], 
											$value[ 'background' ][ 'color' ], 
											$value[ 'background' ][ 'transparent'],
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
										'x2' => $left + $value[ 'size' ][ 'width' ],
										'y1' => $top - $value[ 'coordinate' ][ 'y' ] - ( $value[ 'size' ][ 'height' ] / 2 ),
										'y2' => $top - $value[ 'coordinate' ][ 'y' ] - ( $value[ 'size' ][ 'height' ] / 2 ),
									],
									$value[ 'color' ] );
				}
			}
		}

		// Border
		if( !empty( $this->borderColor ) && $this->borderSize > 0 )
		{
			$this->drawBorder( $image, $this->borderColor, $this->borderSize);
		}

		// 
		if( $format === 'jpeg' || $format === 'jpg' ) { imageJPEG( $image, $fileName ); }
		else if( $format === 'png' ) { imagePNG( $image, $fileName, 6, PNG_NO_FILTER  ); }
		else if( $format === 'gif' ) { imageGIF( $image, $fileName ); }

		//
		imageDestroy( $image );
	}
}
