<?php

namespace Your_Namespace;

use Your_Namespace\Core\Helpers as h;

final class Sample_Class {
    public function __root () {
        h::log( 'called on "plugins_loaded" hook action' );
    }
    public function __init () {
        h::log( 'called on "init" hook action' );
    }
    public function __activation () {
        h::log( 'called when the plugin is activated' );
    }
    public function __deactivation () {
        h::log( 'called when the plugin is deactivated' );
    }
    public function __uninstall () {
        h::log( 'called when the plugin is uninstalled' );
    }
}