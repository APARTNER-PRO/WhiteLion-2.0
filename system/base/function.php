<?php
/**
 * Debug function for output data
 * @param $key
 * @param $trace
 * @param $var_dump
 *
 * @return void
 */
function pp ( $key, $trace = false, $var_dump = false )
{
	if ( WL_MODE == 'prod' && empty( $_COOKIE[ 'debug' ] ) ) {
		return;
	}

	if ( $trace ) {
		$e = new \Exception;
		echo "<pre>";
		$text = $e->getTraceAsString();
		//stripFirstLine
		print_r( substr( $text, strpos( $text, "\n" ) + 1 ) );
		echo "<pre>";
		echo "<hr>";
	} else {
		$trace_ = debug_backtrace();
		$trace = $trace_[ 1 ];
		$trace[ "file" ] = str_replace( $_SERVER[ "DOCUMENT_ROOT" ], "", $trace[ "file" ] );
		echo $trace[ "file" ].':'.$trace[ "line" ].'<br />';
	}

	echo "<pre>";
	if ( empty( $key ) || $var_dump ) {
		var_dump( $key );
	} else {
		print_r( $key );
	}
	echo "</pre>";
}

/**
 * Debug function for output data
 * @param ...$parameters
 *
 * @return void
 */
function mp ( ...$parameters )
{
	if ( WL_MODE == 'prod' && empty( $_COOKIE[ 'debug' ] ) ) {
		return;
	}

	$trace_ = debug_backtrace();
	$trace = $trace_[ 0 ];

	$trace[ "file" ] = str_replace( $_SERVER[ "DOCUMENT_ROOT" ], "", $trace[ "file" ] );

	echo '<br><pre style="background-color: #fff; z-index: 99999; display: block; clear: both;">';
	echo $trace[ "file" ].':'.$trace[ "line" ].'<br />';

	foreach ( $parameters as $i => $ar_ ) {
		if ( $i > 0 ) {
			echo '<br />';
		}

		echo ( $i + 1 ).': ';
		print_r( $ar_ );
	}

	echo "<br />---end</pre><br />";
}

