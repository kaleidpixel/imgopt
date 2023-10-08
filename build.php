<?php
$cmdName  = "imgopt";
$pharFile = $cmdName . '.phar';
$inclides = [
	'vendor',
	'LICENSE',
];

if ( file_exists( $pharFile ) ) {
	unlink( $pharFile );
}
if ( file_exists( $pharFile . '.gz' ) ) {
	unlink( $pharFile . '.gz' );
}

function rmdirAll( $path ) {
	if ( !file_exists( $path ) ) {
		return;
	}

	if ( is_file( $path ) ) {
		unlink( $path );

		return;
	}

	if ( $handle = opendir( $path ) ) {
		while ( false !== ( $item = readdir( $handle ) ) ) {
			if ( $item === '.' || $item === '..' ) {
				continue;
			}
			rmdirAll( $path . DIRECTORY_SEPARATOR . $item );
		}

		closedir( $handle );
		rmdir( $path );
	}
}

function copyAll( $sourceDirectory, $destinationDirectory ) {
	if ( is_file( $sourceDirectory ) === true ) {
		copy( $sourceDirectory, $destinationDirectory );

		return;
	}

	$directory = opendir( $sourceDirectory );

	if ( is_dir( $destinationDirectory ) === false ) {
		mkdir( $destinationDirectory );
	}

	while ( ( $file = readdir( $directory ) ) !== false ) {
		if ( $file === '.' || $file === '..' ) {
			continue;
		}

		if ( is_dir( "$sourceDirectory/$file" ) === true ) {
			copyAll( "$sourceDirectory/$file", "$destinationDirectory/$file" );
		} else {
			copy( "$sourceDirectory/$file", "$destinationDirectory/$file" );
		}
	}

	closedir( $directory );
}

foreach ( $inclides as $file ) {
	$to = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $file;

	copyAll( __DIR__ . DIRECTORY_SEPARATOR . $file, $to );
}

$iterator = new RecursiveDirectoryIterator( __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'kaleidpixel' . DIRECTORY_SEPARATOR . 'image-optimizer' . DIRECTORY_SEPARATOR . 'bin', FileSystemIterator::SKIP_DOTS );
$iterator = new RecursiveIteratorIterator( $iterator );

foreach ( $iterator as $info ) {
	if ( $info->isFile() ) {
		chmod( "{$info->getPathname()}", 0755 );
	}
}

unset( $iterator );

$phar = new Phar( $pharFile );

$phar->startBuffering();

$defaultStub = $phar->createDefaultStub( $cmdName . '.php' );
$stub        = "#!/usr/bin/env php\n" . $defaultStub;

$phar->buildFromDirectory( __DIR__ . DIRECTORY_SEPARATOR . 'src' );
$phar->setStub( $stub );
$phar->stopBuffering();
$phar->compressFiles( Phar::GZ );

foreach ( $inclides as $file ) {
	$to = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $file;

	rmdirAll( $to );
}

echo "$pharFile successfully created" . PHP_EOL;
echo "SHA256 : " . strtoupper( hash_file( 'sha256', realpath( __DIR__ . DIRECTORY_SEPARATOR . "imgopt.phar" ) ) ) . PHP_EOL;

exit( 0 );