<?php

namespace BK\Theme;

use BK\Theme\Controllers\AssetsController;

class Theme {
	/** @var Theme Singleton instance */
	private static Theme $instance;

	/** @var array Instances of controllers */
	private array $controllers = [];

	public function __construct () {
		if (isset(self::$instance)) {
			return;
		}

		self::$instance = $this;

		// TODO - Uncomment if using this theme as a child theme.
		//$this->loadParentTheme();

		$this->controllers = [
			new AssetsController(),
		];
	}

	private function loadParentTheme () {
		add_action('wp_enqueue_scripts', function () {
			//$parentTheme = wp_get_theme('PARENT_THEME');
			//$version     = $parentTheme->get('version');

			wp_enqueue_style(
				'REPLACE_THEME_NAME',
				get_stylesheet_uri(),
				['PARENT_THEME_STYLESHEET'],
				$version ??= false
			);
		});
	}
}
