<?php

include_once __DIR__ . '/helpers.php';

$debug = in_array( '--debug', $argv );
$values = null;
$ready = false;
$prompts = [
	'Plugin Name' => '(e.g. Awesome Plugin)',
	'PHP Namespace' => '(e.g. Foo\Bar)',
];
$defaults = [];

if ( $debug ) {
	$ready = true;
	$values = [
		'Plugin Name' => '__WP_PLUGIN_BASE__',
		'PHP Namespace' => "WP_Plugin_Base\Testing",
	];
}

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
		echo "$key: " . info( $value ) . "\n\r";
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
	echo alert( 'CAUTION' ) . ' ' . info( $dest_dir ) . ' directory already exists.' . PHP_EOL . PHP_EOL;
	// exit( 1 );
	$confirm = strtolower( readline( 'Do you want to delete this directory and all its files? [y/N] ' ) );

	if ( 'y' === $confirm ) {
		rrmdir( $dest_dir );
	} else {
		exit(1);
	}
}

cls();
echo info( 'Copying files ...' ) . PHP_EOL;

// list of files and folders
$all_files = rscandir( $src_dir );
$ignores = [
	'/.git/',
	'/.github/',
	'/vendor/',
	'/.bin/',
	'/.snippets/',
	'/README.md',
	'/changelog.md',
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
	$content = file_get_contents( $file );

	if ( ! file_exists( $target_dir ) ) {
		mkdir( $target_dir, 0755, true );
	}

	// don not change any script (only copy them)
	if ( 'scripts' === basename( $target_dir ) ) {
		file_put_contents( $target, $content );
		continue;
	};

	$filename = basename( $file );
	$context = $find_replace;

	if ( 'composer.json' === $filename ) {
		$context['Your_Namespace'] = str_replace( "\\", "\\\\", $find_replace['Your_Namespace'] );
	}

	$content = str_replace(
		array_keys( $context ),
		array_values( $context ),
		$content
	);

	file_put_contents( $target, $content );
}

chdir( $dest_dir );

if ( shell_cmd_exists( 'wp' ) ) {
	echo info( "Generating languages/{$find_replace['your_text_domain']}.pot file ..." ) . PHP_EOL;
	$output = shell_exec( 'composer run make-pot' );
	if ( $debug ) echo $output;
}

// install dependencies via composer
if ( shell_cmd_exists( 'composer' ) && ! file_exists( "$dest_dir/vendor" ) ) {
	echo info( 'Installing composer autoloader ...' ) . PHP_EOL;
	$output = shell_exec( 'composer update' );
	if ( $debug ) echo $output;
}

if ( $debug ) {
	echo info( "Files generated:" ) . PHP_EOL;
	echo shell_exec( 'ls -Apl' );
}

echo PHP_EOL . success( "Your plugin was successfully created in $dest_dir" ) . PHP_EOL;

file_put_contents( $src_dir . '/install.log', $dest_dir );
