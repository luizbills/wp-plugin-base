<?php
/*
This file should return a array of classes names (or instances) that you want to run automatically when the plugin is ready to work (if all dependencies in the `/config/dependencies.php` file are satisfied).

Each index should be an array (with the class name/instance and an integer to indicate priority) ou just the class name (or instance). By default, each class has priority 10.
*/

namespace Your_Namespace;

// prevents your PHP files from being executed via direct browser access
defined( 'WPINC' ) || exit( 1 );

return [
	[ Sample_Class::class, 10 ], // 10 is priority
];
