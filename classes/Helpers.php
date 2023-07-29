<?php

namespace Your_Namespace;

use Your_Namespace\Core\Traits\Common_Helpers;
use Your_Namespace\Core\Traits\Config_Helpers;
use Your_Namespace\Core\Traits\Debug_Helpers;
use Your_Namespace\Core\Traits\String_Helpers;
use Your_Namespace\Core\Traits\Template_Helpers;
use Your_Namespace\Core\Traits\Throw_Helpers;
use Your_Namespace\Core\Traits\WordPress_Helpers;

use Your_Namespace\Helpers as h;

/**
 * This class contains several useful helpers for your plugin.
 * Learn more in /core/Traits folder.
 * But their are totally optionals, so you can
 * remove any Trait below or even this entire class.
*/
abstract class Helpers {

	// See the /core/Traits to learn the helpers
	use Common_Helpers,
		Config_Helpers,
		Debug_Helpers,
		String_Helpers,
		Template_Helpers,
		Throw_Helpers,
		WordPress_Helpers;

	// Your custom helpers (always STATIC)
	// public static function foo () {
	//     return 'bar';
	// }
}
