<?php
	require_once '../src/Options.php';
	require_once '../src/Utils.php';
	require_once '../src/Draw.php';
	require_once '../src/PosterGen.php';

	function array_get( $array, $key, $default = null )
    {
        if( is_null( $key ) ) { return $array; }
		if( isset( $array[ $key ] ) ) { return $array[ $key ]; }
		
        foreach( explode( '.', $key ) as $segment )
        {
            if( !is_array( $array ) || !array_key_exists( $segment, $array ) )
            {
                return ( $default instanceof Closure ? $default( ) : $default );
			}
			
            $array = $array[ $segment ];
		}
		
        return $array;
    }

	/**
	 * 
	 */
	$request = $_REQUEST;
	$width = array_get( $request, 'width', 500 );
	$height = array_get( $request, 'height', 500 );

	/**
	 * 
	 */
	$poster = ( new \PosterGen\PosterGen( [ ] ) )
				->setSize( $width, $height )
				->setBackgroundColor( array_get( $request, 'background-color' ) )
				->setFont( __DIR__ . "/fonts/ComicRelief" );

	/**
	 * 
	 */
	$backgroundImage = array_get( $request, 'background-image' );
	if( !empty( $backgroundImage ) ) { $poster->setBackgroundImage( $backgroundImage ); }

	/**
	 * 
	 */
	for( $i = 0; $i < count( $request[ 'text' ] ); $i++ )
	{
		$text = array_get( $request[ 'text' ] , $i, '' );
		$size = array_get( $request[ 'size' ] , $i, 0 );
		$color = array_get( $request[ 'color' ] , $i, '' );

		$poster->addText( $text, '', $size, $color, [
			'position'	=> [
				'vertical-alignment' => array_get( $request[ 'vertical-alignment' ] , $i, '' ),
				'horizontal-alignment' => array_get( $request[ 'horizontal-alignment' ] , $i, '' ),
			]
		] );
	}

	/**
	 * 
	 */
	echo $poster->saveToBase64Image( );
?>
