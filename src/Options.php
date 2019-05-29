<?php
namespace PosterGen;

trait Options
{
	protected $debug = false;
	protected $size = [ 'width' => 300, 'height' => 150 ];

	protected $font = '';
	protected $fontColor = '#000000';
	protected $fontSize = 14;
	protected $fontStyle = [ ];
	protected $linePadding = 8;

	protected $backgroundImage = '';
	protected $backgroundTransparent = 100;
	protected $backgroundColor = '#FFFFFF';

	protected $backgroundGradient = [ ]; // 4 colors ( 1 corner = 1 color )

	protected $overlayColor = '';
	protected $overlayTransparent = 100;

	protected $borderColor = '';
	protected $borderSize = 1;

	protected $strokeColor = '';
	protected $strokeSize = 1;

	protected $shadowColor = '';
	protected $shadowOffset = [ ];

	protected $textBackgroundColor = '';
	protected $textBackgroundTransparent = 0;

	protected $horizontalAlignment = 'center';
	protected $verticalAlignment = 'center';

	protected $verticalPadding = 10;
	protected $horizontalPadding = 10;

	protected $objectList = [ ];

	function __construct( array $options = [ ] )
	{

	}

	/**
	 * 
	 */

	function setSize( /*int*/ $width = 300, /*int*/ $sectionsHeight = 150 )
	{
		$this->size = [ 
			'width' => $width, 
			'height' => $sectionsHeight
		];

		return $this;
	}

	function getSize( /*bool*/ $realSize = False )
	{
		return [ 
			'width' => $this->size[ 'width' ] - ( $realSize ? 0 : $this->verticalPadding ), 
			'height' => $this->size[ 'height' ] - ( $realSize ? 0 : $this->horizontalPadding ), 
		];
	}

	function setFont( /*string*/ $value )
	{
		$fontFile = $value . ( empty( pathinfo( $value )[ 'extension' ] ) ? '.ttf' : '' );

		if( file_exists( $fontFile ) )
		{
			$this->font = $fontFile;
		}
		else
		{
			throw new \Exception( "PosterGen: No font available: {$fontFile}!" );
		}

		return $this;
	}

	function setFontColor( /*string*/ $value )
	{
		$this->fontColor = $value;

		return $this;
	}

	function setFontSize( /*int*/ $value )
	{
		$this->fontSize = $value;

		return $this;
	}

	function setFontStyle( array $value )
	{
		$this->fontStyle = $value;

		return $this;
	}

	function setFontShadow( $color, /*int*/ $x = 0, /*int*/ $y = 0 )
	{
		$this->shadowColor = $color;
		$this->shadowOffset = [ 'x' => $x, 'y' => $y ];

		return $this;
	}

	function setFontStroke( $color, /*int*/ $size = 1 )
	{
		$this->strokeColor = $color;
		$this->strokeSize = $size;
		
		return $this;
	}

	function setLinePadding( /*int*/ $value )
	{
		$this->linePadding = $value;

		return $this;
	}

	function setVerticalPadding( /*int*/ $value )
	{
		$this->verticalPadding = $value;

		return $this;
	}

	function setHorizontalPadding( /*int*/ $value )
	{
		$this->horizontalPadding = $value;

		return $this;
	}

	function setBackgroundImage( /*string*/ $image, /*int*/ $transparent = 0 )
	{
		if( file_exists( $image )
			|| ( filter_var( $image, FILTER_VALIDATE_URL ) && @get_headers( $image )[0] !== 'HTTP/1.0 404 Not Found' ) ) 
		{
			$this->backgroundImage = $image;
			if( $transparent > 0 ) { $this->backgroundTransparent = $transparent; }
		}
		else
		{
			throw new \Exception( "PosterGen: No image available: {$image}!" );
		}

		return $this;
	}

	function setBackgroundColor( $color )
	{
		$this->backgroundColor = $color;

		return $this;
	}

	function setBackgroundGradient( array $colors )
	{
		$this->backgroundGradient = $colors;

		return $this;
	}

	function setOverlayColor( $color, /*int*/ $transparent = 40 )
	{
		$this->overlayColor = $color;
		$this->overlayTransparent = $transparent;

		return $this;
	}

	function setVerticalAlignment( /*string*/ $value )
	{
		$this->verticalAlignment = $value;

		return $this;
	}

	function setHorizontalAlignment( /*string*/ $value )
	{
		$this->horizontalAlignment = $value;

		return $this;
	}

	function setBorder( $color, /*int*/ $size )
	{
		$this->borderColor = $color;
		$this->borderSize = $size;

		return $this;	
	}

	function setTextBackground( $color, /*int*/ $transparent = 0 )
	{
		$this->textBackgroundColor = $color;
		$this->textBackgroundTransparent = $transparent;

		return $this;
	}

	function debug( /*bool*/ $value )
	{
		$this->debug = $value;

		return $this;
	}
}