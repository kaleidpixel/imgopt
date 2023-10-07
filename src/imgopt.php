#!/usr/bin/env php
<?php
set_time_limit( 0 );

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use KALEIDPIXEL\Module\ImageOptimizer;

$shortopts = "p:";
$longopts  = array(
	"path:",
);
$options   = getopt( $shortopts, $longopts );
$phar_name = basename( __FILE__, '.php' );
$path      = null;

if ( !empty( $options ) && !in_array( 'p' || 'path', array_keys( $options ), true ) ) {
	$path = realpath( empty( $options['p'] ) ? $options['path'] : $options['p'] );
}

if ( !empty( $path ) ) {
	$optimizer              = ImageOptimizer::get_instance();
	$optimizer->image_dir   = $path;
	$optimizer->command_dir = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'kaleidpixel' . DIRECTORY_SEPARATOR . 'image-optimizer' . DIRECTORY_SEPARATOR . 'bin';
	$optimizer->phar_name   = $phar_name;

	$optimizer->doing();
}

$json      = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'kaleidpixel' . DIRECTORY_SEPARATOR . 'image-optimizer' . DIRECTORY_SEPARATOR . 'composer.json';
$phar_name = ucfirst( $phar_name );
$date      = date( "F d Y H:i", filemtime( $json ) );
$package   = json_decode( file_get_contents( $json ) );
$colors    = [
	'black'   => "\e[0;30m",
	'red'     => "\e[0;31m",
	'green'   => "\e[0;32m",
	'yellow'  => "\e[0;33m",
	'blue'    => "\e[0;34m",
	'magenta' => "\e[0;35m",
	'cyan'    => "\e[0;36m",
	'white'   => "\e[0;37m",
	'reset'   => "\e[0m"
];
$str       = <<<EOL
{$colors['green']}$phar_name{$colors['reset']} version {$colors['yellow']}$package->version{$colors['reset']} $date

{$colors['yellow']}Options:{$colors['reset']}
  {$colors['green']}-p, --path{$colors['reset']}    Specify the directory path where the images you want to optimize are stored

EOL;

print_r( $str );
unset( $shortopts, $longopts, $options, $path, $json, $date, $package, $colors, $str );
exit( 0 );
