<?php
$cmdName      = "imgopt";
$pharFile     = $cmdName . '.phar';
$pharFilePath = __DIR__ . DIRECTORY_SEPARATOR . $pharFile;
$inclides     = [
	'vendor',
	'LICENSE',
];
$winBinPath   = __DIR__ . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'win';
$zipPath      = __DIR__ . DIRECTORY_SEPARATOR . "$cmdName-x64-win";
$zipArchive   = [
	[ $winBinPath . DIRECTORY_SEPARATOR . $cmdName, $cmdName ],
	[ $winBinPath . DIRECTORY_SEPARATOR . $cmdName . '.bat', $cmdName . '.bat' ],
	[ $pharFilePath, $pharFile ],
];
$zip          = new ZipArchive;

if ( file_exists( $pharFile ) ) {
	unlink( $pharFile );
}

if ( file_exists( $pharFile . '.gz' ) ) {
	unlink( $pharFile . '.gz' );
}

function rmdirAll( $path ): void {
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

function copyAll( $sourceDirectory, $destinationDirectory ): void {
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

file_put_contents( "$pharFilePath.md5", hash_file( 'md5', realpath( $pharFilePath ) ) );
file_put_contents( "$pharFilePath.sha256", hash_file( 'sha256', realpath( $pharFilePath ) ) );
file_put_contents( "$pharFilePath.sha512", hash_file( 'sha512', realpath( $pharFilePath ) ) );

// Create an archive for Scoop Bucket.
if ( is_dir( $zipPath ) === false ) {
	mkdir( $zipPath, 0755 );
}

if ( $zip->open( $zipPath . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE ) === true ) {
	foreach ( $zipArchive as $val ) {
		$zip->addFile( $val[0], $val[1] );
	}

	$zip->close();

	file_put_contents( "$zipPath.zip.md5", hash_file( 'md5', realpath( $zipPath . '.zip' ) ) );
	file_put_contents( "$zipPath.zip.sha256", hash_file( 'sha256', realpath( $zipPath . '.zip' ) ) );
	file_put_contents( "$zipPath.zip.sha512", hash_file( 'sha512', realpath( $zipPath . '.zip' ) ) );
} else {
	print_r( 'Failed to create the zip file.' );
}

exit( 0 );
