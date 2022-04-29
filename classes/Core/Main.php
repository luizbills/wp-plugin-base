<?php

namespace Your_Namespace\Core;

use Your_Namespace\Core\Helpers as h;

final class Main {
    protected static $classes_to_load = [];
    protected static $dependencies = [];

    // == CORE HELPERS ==
    public static function run_plugin ( $main_file ) {
        h::throw_if(
            h::config_get( 'FILE', false ),
            __CLASS__ . ' already inialized'
        );

        h::throw_if(
            ! file_exists( $main_file ),
            'Invalid plugin main file path'
        );

        $root = dirname( $main_file );
        h::config_set( 'FILE', $main_file );
        h::config_set( 'ROOT', $root );

        if ( file_exists( $root . '/config.php' ) ) {
            $values = include $root . '/config.php';
            foreach ( $values as $k => $v ) {
                if ( 'SLUG' === \mb_strtoupper( $k ) ) {
                    $v = \sanitize_title_with_dashes( $v );
                }
                h::config_set( $k, $v );
            }
            $slug = h::config_get( 'slug' );
            if ( ! h::config_get( 'prefix', false ) ) {
                h::config_set( 'prefix', str_replace( '-', '_', $slug ) . '_' );
            }
        }

        if ( \file_exists( $root . '/composer.json' ) ) {
            $json_raw = \file_get_contents( $root . '/composer.json' );
            $composer = \json_decode( $json_raw, true );
            $php_version = $composer ? h::array_get( $composer, [ 'require', 'php' ], '' ) : false;
            if ( $php_version ) {
                $php_version = \preg_replace( '/[^0-9\.]/', '', $php_version );
                h::config_set( 'REQUIRED_PHP_VERSION', $php_version );
            }
        }

        if ( file_exists( $root . '/dependencies.php' ) ) {
            self::register_dependencies();
        } else {
            h::config_set( 'PLUGIN_ACTIVATED', true );
        }

        if ( file_exists( $root . '/loader.php' ) ) {
            self::setup_loader();
        }

        self::load_plugin_textdomain( 'your_text_domain' );
    }

    public static function load_plugin_textdomain ( $text_domain ) {
		$dir = h::config_get( 'LANGUAGES_DIR', 'languages' );
        \load_plugin_textdomain(
			$text_domain,
			false,
			\dirname( \plugin_basename( h::config_get( 'FILE' ) ) ) . "/$dir/"
		);
	}

    // == DEPENDECIES CHECKER ==
    public static function register_dependencies () {
        self::$dependencies[] = include_once $root . '/dependencies.php';
        h::throw_if(
            ! is_array( self::$dependencies ),
            h::config_get( 'ROOT' ) . '/dependencies.php must return an Array'
        );
        \add_action( 'plugins_loaded', [ __CLASS__, 'check_dependencies' ], 0 );
    }

    public static function check_dependencies () {
        $errors = [];
        foreach ( self::$dependencies as $key => $dep ) {
            $check = is_callable( $dep['check'] ) ? call_user_func( $dep['check'] ) : $dep['check'];
            if ( ! $check ) {
                $errors[ $key ] = $dep['message'];
            }
        }
        if ( count( $errors ) > 0 ) {
            \add_action( 'admin_notices', function () use ( $errors ) {
                $allowed_html = [
                    'a' => [
                        'href' => [],
                        'title' => [],
                    ],
                    'br' => [],
                    'em' => [],
                    'strong' => [],
                ];
                list( $plugin_name ) = \get_file_data( h::config_get( 'file' ), [ 'plugin name' ] );
                echo "<div class='notice notice-error'><p>Não foi possível ativar o plugin <strong>$plugin_name</strong>. Siga as instruções abaixo:";
                foreach ( $errors as $error_message ) {
                    $line = \sprintf(
                        '<br>%s• %s',
                        \str_repeat( '&nbsp;', 8 ),
                        $error_message
                    );
                    echo \wp_kses( $line, $allowed_html );
                }
                echo '</p></div>';
            } );
        }
        h::config_set( 'PLUGIN_ACTIVATED', count( $errors ) === 0 );
    }

    public static function has_dependencies () {
        return count( self::$dependencies ) > 0;
    }

    // CLASS LOADER
    public static function load_classes () {
        if ( ! h::config_get( 'PLUGIN_ACTIVATED', null ) ) return;
        if ( ! self::$classes_to_load ) return;
        $main_file = h::config_get( 'FILE' );
        foreach ( self::$classes_to_load as $item ) {
            $class_name = $item[0];
            $priority = $item[1];
            h::throw_if(
                ! \class_exists( $class_name ),
                'class ' . $class_name . ' does not exist'
            );
            $instance = new $class_name();
            if ( \method_exists( $class_name, '__boot' ) ) {
                $instance->__boot();
            }
            if ( \method_exists( $class_name, '__init' ) ) {
                \add_action( 'init', [ $instance, '__init' ], $priority );
            }
            if ( \method_exists( $class_name, '__activation' ) ) {
                \register_activation_hook( $main_file, [ $instance, '__activation' ] );
            }
            if ( \method_exists( $class_name, '__deactivation' ) ) {
                \register_deactivation_hook( $main_file, [ $instance, '__deactivation' ] );
            }
            if ( \method_exists( $class_name, '__uninstall' ) ) {
                \register_uninstall_hook( $main_file, [ $instance, '__uninstall' ] );
            }
        }
        self::$classes_to_load = null;
    }

    protected static function setup_loader () {
        $classes = include_once $root . '/loader.php';

        foreach ( $classes as $class ) {
            if ( ! is_array( $class ) ) {
                $class = [ $class, 10 ];
            }
            self::$classes_to_load[] = $class;
        }
        usort( self::$classes_to_load, function ( $a, $b ) {
            return $a[1] > $b[1];
        });
        \add_action( 'plugins_loaded', [ __CLASS__, 'load_classes' ], 10 );
    }
}