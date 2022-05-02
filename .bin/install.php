<?php

include_once __DIR__ . '/helpers.php';

$values = null;
$ready = false;
$prompts = [
	'Plugin Name' => '(e.g. Awesome Plugin)',
	'PHP Namespace' => '(e.g. Foo\Bar)',
];
$defaults = [];

$ready = true;
$values = [
	'Plugin Name' => 'Test',
	'PHP Namespace' => 'Test\Plugin',
];

// get some plugin informations
while ( ! $ready ) {
	$values = [];

	foreach ( $prompts as $var => $desc ) {
		$value = '';
		while ( empty( $value ) ) {
			cls();
			$value = readline( "$var $desc: " );
			if ( empty( $value ) && isset( $defaults[ $var ] ) ) {
				$value = $defaults[ $var ];
			}
		}
		$values[ $var ] = $value;
	}

	cls();

	foreach ( $values as $key => $value ) {
		echo "$key: $value\n\r";
	}

	// give the user a chance to fix the informations
	$confirm = strtolower( readline( 'Is it OK? [Y/n] ' ) );

	$ready = '' === $confirm || 'y' === $confirm;
}

cls();

// placeholders to find and to replace
$find_replace = [
	'Your_Namespace' => $values['PHP Namespace'],
	'your_plugin_name' => htmlspecialchars( $values['Plugin Name'] ),
	'your_text_domain' => slugify( $values['Plugin Name'] ),
	'your_plugin_slug' => slugify( $values['Plugin Name'] ),
	'your_plugin_prefix' => prefixify( $values['Plugin Name'] ),
];

// useful informations
$slug = $find_replace['your_plugin_slug'];
$src_dir = dirname( dirname( __FILE__ ) );
$dest_dir = dirname( $src_dir ) . '/' . $slug;

// remove old plugin
if ( file_exists( $dest_dir ) && is_dir( $dest_dir ) ) {
	echo "Warning: $dest_dir already exists." . PHP_EOL;
	// exit( 1 );
	$confirm = strtolower( readline( 'Do you want to delete this directory and all its files? [y/N] ' ) );

	if ( 'y' === $confirm ) {
		rrmdir( $dest_dir );
	} else {
		exit(1);
	}
}

// list of files and folders
$all_files = rscandir( $src_dir );
$ignores = [
	'/.git/',
	'/vendor/',
	'/.bin/',
	'/README.md',
	'/composer.lock',
];
$files = [];

foreach ( $all_files as $file ) {
	$ignored = false;
	foreach ( $ignores as $ignore ) {
		$find = strpos( $file, $ignore );
		if ( false !== strpos( $file, $ignore ) ) {
			$ignored = true;
			break;
		}
	}
	if ( ! $ignored ) $files[] = $file;
}

mkdir( $dest_dir, 0755 );

// copy src files and replace variables
foreach ( $files as $file ) {
	$target = str_replace( $src_dir, $dest_dir, $file );
	$target_dir = dirname( $target );
	$filename = basename( $file );

	if ( ! file_exists( $target_dir ) ) {
		mkdir( $target_dir, 0755, true );
	}

	if ( 'composer.json' === $filename ) {
		$find_replace['Your_Namespace'] = str_replace( "\\", "\\\\", $find_replace['Your_Namespace'] );
	}

	$content = file_get_contents( $file );
	$content = str_replace(
		array_keys( $find_replace ),
		array_values( $find_replace ),
		$content
	);

	if ( 'composer.json' === $filename ) {
		$find_replace['Your_Namespace'] = str_replace( "\\\\", "\\", $find_replace['Your_Namespace'] );
	}

	file_put_contents( $target, $content );
}

// install dependencies via composer
if ( ! file_exists( "$dest_dir/vendor" ) ) {
	echo 'Installing composer packages ...' . PHP_EOL;
	chdir( $dest_dir );
	echo shell_exec( 'composer update' );
}

echo PHP_EOL . "The plugin was successfully created in $dest_dir" . PHP_EOL . PHP_EOL;

chdir( $dest_dir );
echo shell_exec( 'ls -Apl' );

file_put_contents( $src_dir . '/' . '.newplugin', $dest_dir );
