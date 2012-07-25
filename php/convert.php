<?php

require_once dirname( __FILE__ ) . '/spyc.php';

function parse_csv( $file ) {
	
	$lines = explode( "\n", file_get_contents( "{$file}.csv" ) );
	$headers = str_getcsv( array_shift( $lines ) );
	$data = array();
	
	foreach ( $lines as $line ) {
	
		$row = array();
	
		foreach ( str_getcsv( $line ) as $key => $field )
			$row[ $headers[ $key ] ] = $field;
	
		$data[] = $row;
	
	}
	
	return $data;
	
}

function build_name( $name ) {
	
	$name = strtolower( $name );
	$name = preg_replace( '/[^a-z0-9-]/', '-', $name );
	$name = str_replace( '--', '-', $name );
	return $name;
	
}

function build_file( $fields, $name_field, $dir ) {

	$name = build_name( $fields[ $name_field ] );
	
	foreach ( array( 'json', 'xml' ) as $format ) {
		
		$fields[ 'layout' ] = $format;
		$output = Spyc::YAMLDump( $fields );
		$output .= '---';
		
		file_put_contents( "{$dir}/{$name}.{$format}", $output );
		
	}

}

$config = spyc::YAMLLoad( '../_config.yml' );
$file = isset( $config['source_file'] ) ? $config['source_file'] : $config[ 'record_plural' ]; 
$data = parse_csv( "../raw/{$file}" );
$dir = '../' . $config['record_plural'];
@mkdir( $dir );

foreach( $data as $file )
	build_file( $file, $config['name_field'], $dir );
