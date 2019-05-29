<?php 
namespace PosterGen;

/**
 * Get an item from an array using "dot" notation.
 * https://github.com/rappasoft/laravel-helpers/blob/master/src/helpers.php
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $default
 * @return mixed
 */
function array_get( $array, $key, $default = null )
{
	if( is_null( $key ) ) { return $array; }
	if( isset( $array[ $key ] ) ) { return $array[ $key ]; }

	foreach( explode( '.', $key ) as $segment )
	{
		if( !is_array( $array ) || !array_key_exists( $segment, $array ) )
		{
			return $default;
		}

		$array = $array[ $segment ];
	}

	return $array;
}