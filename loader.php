<?php

namespace Your_Namespace;

use Your_Namespace\Helpers as h;

defined( 'WPINC' ) || exit( 1 );

// register_activation_hook( h::config_get( 'FILE' ), function () {
// 	h::log( 'plugin activated' );
// } );

return [
	[ Sample_Class::class, 10 ], // 10 is priority
];
