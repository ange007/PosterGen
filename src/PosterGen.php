<?php
namespace PosterGen;

class PosterGen
{
	use Options;
	use Utils;
	use Draw;

	function __construct( array $options = [ ] )
	{

	}

	/**
	 * 
	 */
	function getLastTextCoordinates( )
	{
		return $lastTextCoordinate;
	}

	/**
	 * 
	 */
	function getLastImageCoordinates( )
	{
		return $lastImageCoordinate;
	}

	/**
	 * 
	 */
	function addText( /*string*/ $text, /*string*/ $font = '', /*int*/ $size = 0, /*string*/ $color = '', array $style = [ ], array $values = [ ] )
	{
		// Font params
		$font = ( !empty( $font ) ? $font : $this->font );
		$font .= empty( pathinfo( $font )[ 'extension' ] ) ? '.ttf' : '';
		$color = ( !empty( $color ) ? $color : $this->fontColor );
		$size = ( $size > 0 ? $size : $this->fontSize );
		$style = ( !empty( $style ) ? $style : $this->fontStyle );

		// Angle
		$angle = array_get( $values, 'angle', 0 );
		$transparent = array_get( $values, 'transparent', 100 );

		// Text background
		$background = [ 
			'color'			=> ( array_key_exists( 'background', $values ) && array_key_exists( 'color', $values[ 'background' ] ) ) ? $values[ 'background' ][ 'color' ] : $this->textBackgroundColor,
			'transparent'	=> ( array_key_exists( 'background', $values ) && array_key_exists( 'transparent', $values[ 'background' ] ) ) ? $values[ 'background' ][ 'transparent' ] : $this->textBackgroundTransparent
		];

		// Position values
		$position = array_get( $values, 'position', [ ] );
		if( empty( $position[ 'vertical-alignment' ] ) && empty( $position[ 'x' ] ) && empty( $position[ 'y' ] ) ){ $position[ 'vertical-alignment' ] = $this->verticalAlignment; };
		if( empty( $position[ 'horizontal-alignment' ] ) && empty( $position[ 'x' ] ) && empty( $position[ 'y' ] ) ){ $position[ 'horizontal-alignment' ] = $this->horizontalAlignment; };

		//
		$data = array_replace_recursive( [
			'type'			=> 'text',
			// 'text'			=> null,
			'font'			=> $font,
			'font-size'		=> $size,
			'color'			=> $color,
			'style'			=> $style,
			'stroke'		=> [ 
				'color' 	=> $this->strokeColor,
				'size'		=> $this->strokeSize
			],
			// 'size'		=> [ ],
			'position'		=> $position,
			// 'coordinate'	=> [ ],
			'shadow'		=> [ 
				'color' 	=> $this->shadowColor,
				'offset'	=> $this->shadowOffset
			],
			'angle'			=> $angle,
			'transparent'	=> $transparent,
			'background'	=> $background
		], $values );

		// 
		$linesArray = explode( "\r\n", $text );

		//
		for( $l = 0; $l < count( $linesArray ); $l++ )
		{
			$wrappedText = '';
			$wordArray = explode( ' ', $linesArray[ $l ] );

			//
			for( $i = 0; $i < count( $wordArray ); $i++ )
			{
				$word = $wordArray[ $i ];
				$textBox = $this->imageTTFBBoxExtended( $size, 0, $font, $wrappedText . ' ' . $word );
				
				if( $textBox[ 'width' ] < $this->getSize( true )[ 'width' ] )
				{ 
					$wrappedText .= ( $wrappedText === '' ? '' : ' ' ) . $word; 
				}
				else
				{
					$this->addTextLine( $wrappedText, $data );
					$wrappedText = $word;
				}

				if( $i === count( $wordArray ) - 1 ) 
				{ 
					$this->addTextLine( $wrappedText, $data );
				}
			}
		}

		return $this;
	}

	/**
	 * 
	 */
	private function addTextLine( /*string*/ $text, array $values = [ ] )
	{
		// Calculate coordinates
		$coordinate = $this->calculateTextCoordinates( $text, $values[ 'font-size' ], $values[ 'position' ], $values[ 'color' ], $values[ 'angle' ], $values[ 'font' ] );

		//
		$data = array_replace_recursive( [
			'text'			=> $text,
			'size'			=> [
				'width'		=> $coordinate[ 'width' ],
				'height'	=> $coordinate[ 'height' ],
			],
			'coordinate'	=> $coordinate,
		], $values );

		//
		array_push( $this->objectList, $data );
		
		return $this;
	}

	/**
	 * 
	 */
	function addImage( /*string*/ $image, array $values = [ ] )
	{
		if( !file_exists( $image ) )
		{
			throw new \Exception( "PosterGen: No image available: {$image}!" );
		}

		// Image
		$customImage = imageCreateFromString( file_get_contents( $image ) );

		// Angle
		$angle = array_get( $values, 'angle', 0 );
		$inline = array_get( $values, 'inline', false );
		$transparent = array_get( $values, 'transparent', 100 );

		// Image size
		$size = $this->calculateImageSize( $customImage );

		// Position values
		$position = array_get( $values, 'position', [ ] );
		if( empty( $position[ 'vertical-alignment' ] ) && empty( $position[ 'x' ] ) && empty( $position[ 'y' ] ) ){ $position[ 'vertical-alignment' ] = $this->verticalAlignment; };
		if( empty( $position[ 'horizontal-alignment' ] ) && empty( $position[ 'x' ] ) && empty( $position[ 'y' ] ) ){ $position[ 'horizontal-alignment' ] = $this->horizontalAlignment; };		
		$x = array_get( $position, 'x', 0 );
		$y = array_get( $position, 'y', 0 );

		//
		$data = array_replace_recursive( [
			'type'			=> 'image',
			'image'			=> $image,
			'size'			=> $size,
			'position'		=> $position,
			'coordinate'	=> [
				'top'		=> $x,
				'bottom'	=> $x + $size[ 'height' ],
				'left'		=> $y,
				'right'		=> $y + $size[ 'width' ],
			],
			'angle'			=> $angle,
			'transparent'	=> $transparent,
			'inline'		=> false
		], $values );

		//
		array_push( $this->objectList, $data );
		
		return $this;
	}

	/**
	 * 
	 */
	function addSeparator( /*string*/ $color = '', /* string */ $align = 'center' )
	{
		$values = [
			'position' => [
				'horizontal-alignment' => $align
			]
		];

		return $this->addText( '-', '', 0, $color, [ ], $values );
	}

	/**
	 * 
	 */
	function save( /*string*/ $format = 'png' )
	{
		header( "Content-type: image/{$format}" );

		//
		$this->generate( $format );
	}

	/**
	 * 
	 */
	function saveToBase64( /*string*/ $format = 'png' )
	{
		return 'data:image/png;base64,' . base64_encode( $this->output( ) );
	}

	/**
	 * 
	 */
	function saveToBase64Image( /*string*/ $format = 'png' )
	{
		return '<img src="' . $this->saveToBase64( ) . '"/>';
	}

	/**
	 * 
	 */
	function output( /*string*/ $format = 'png' )
	{
		ob_start( );

		//
		$this->generate( $format );

		//
		$imageData = ob_get_contents( );
		ob_end_clean( );

		//
		return $imageData;
	}
}