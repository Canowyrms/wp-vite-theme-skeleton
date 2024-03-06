<?php

declare(strict_types=1);

namespace BK\Theme;

/*
use BK\Theme\Hooks\{
	Activator,
	Deactivator,
	Uninstaller
};
*/

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Theme constants
 */

//define('BK_THEME_CONST', '');


/**
 * Theme hooks
 * 
 * TODO - I'm not even sure if themes make use of these hooks.
 */

//@fmt:off
//register_activation_hook(  __FILE__, fn () => Activator::init());   // TODO - Theme activator ?
//register_deactivation_hook(__FILE__, fn () => Deactivator::init()); // TODO - Theme deactivator ?
//register_uninstall_hook(   __FILE__, fn () => Uninstaller::init()); // TODO - Theme uninstaller ?
//@fmt:on


/**
 * Boot up
 */

$theme = new Theme();


/**
 * Testing
 */

// Placeholder.
