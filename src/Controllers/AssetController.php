<?php

namespace BK\Theme\Controllers;

class AssetsController {
	/** @var AssetsController Singleton instance */
	private static AssetsController $instance;

	private bool $viteDev = false;

	private array|false $manifest;

	public function __construct () {
		if (isset(self::$instance)) {
			return;
		}

		self::$instance = $this;

		$this->checkViteDev();
		$this->loadManifest();

		if (is_admin()) {
			//@fmt:off
			//add_action('admin_enqueue_scripts',      fn () => $this->adminScripts(),    9999);
			add_action('admin_head',                 fn () => $this->adminHeadHTML(),   9999);
			//add_action('admin_print_footer_scripts', fn () => $this->adminFooterHTML(), 9999);
			//@fmt:on
		} else {
			//@fmt:off
			//add_action('wp_enqueue_scripts', fn () => $this->publicScripts(),    9999);
			add_action('wp_head',            fn () => $this->publicHeadHTML(),   9999);
			//add_action('wp_footer',          fn () => $this->publicFooterHTML(), 9999);
			//@fmt:on
		}
	}

	/* Vite Helpers */

	/**
	 * Makes a cURL request to Vite's dev server and sets self::$viteDev
	 *   to true if it gets a response.
	 */
	private function checkViteDev () {
		if (!in_array(wp_get_environment_type(), ['development', 'local'])) {
			return;
		}

		$curl = curl_init('http://localhost:5173/');
		//@fmt:off
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, 10);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,    true);
		curl_setopt($curl, CURLOPT_NOBODY,            true);
		//@fmt:on

		curl_exec($curl);

		$this->viteDev = !curl_errno($curl);

		curl_close($curl);
	}

	/**
	 * Vite produces an asset manifest in JSON format -- read and parse it.
	 *
	 * TODO - test loadManifest
	 * TODO - Throw error if manifest can't be found/loaded.
	 */
	private function loadManifest () {
		$file = get_stylesheet_directory() . '/assets/manifest.json';

		if (!file_exists($file)) {
			// TODO - Warn of missing manifest.
			return;
		}

		$this->manifest = json_decode(file_get_contents($file), true);
	}

	/**
	 * Reusable function to output a script tag for Vite client.
	 */
	private function maybeOutputViteClientMarkup () {
		if (!$this->viteDev) {
			return;
		}

		?>
		<script type="module" crossorigin src="http://localhost:5173/@vite/client"></script>
		<?php
	}

	/**
	 * Concatenates numerous string parts to form a full URL to an asset.
	 *
	 * @param string $file Asset filename; almost certainly includes a unique hash.
	 *
	 * @return string Full URL to the asset.
	 */
	private function createAssetURL (string $file) : string {
		return get_stylesheet_directory_uri() . '/assets/' . $file;
	}

	/**
	 * Assets passed to this function should be entry-points in vite.config.js
	 *   (build > rollupOptions > input).
	 *
	 * Assets processed by Vite have entries in the asset manifest which
	 *   may contain information about other assets we may want to load
	 *   (images, stylesheets, etc.).
	 *
	 * When developing with Vite, the browser will utilize the module syntax
	 *   to import assets when they're needed.
	 *
	 * TODO - Consider throwing error instead of silently failing.
	 * TODO - Test imports and css if statements.
	 *
	 * @param string $asset
	 */
	private function outputAssetMarkup (string $asset) {
		/**
		 * If Vite's dev server is running, we can request the asset directly
		 *   from it and return early.
		 */
		if ($this->viteDev) {
			?>
			<script type="module" crossorigin src="http://localhost:5173/<?= $asset; ?>"></script>
			<?php

			return;
		}

		if (!isset($this->manifest[$asset])) {
			// TODO - Warn asset does not exist.
			return;
		}

		$entry = $this->manifest[$asset];

		?>
		<script type="module" src="<?= $this->createAssetURL($entry['file']) ?>"></script>
		<?php

		/**
		 * The requested asset imports other assets; we can add a module preload
		 *   tag and have the asset ready if/when the browser needs it.
		 *
		 * Imports are prefixed with '_' and correspond to a top-level entry in
		 *   the manifest.
		 */
		if (isset($entry['imports']) && !empty($entry['imports'])) {
			foreach ($entry['imports'] as $import) {
				$importEntry = $this->manifest[$import];

				?>
				<link rel="modulepreload" href="<?= $this->createAssetURL($importEntry['file']); ?>">
				<?php
			}
		}

		/**
		 * The requested asset has one or more stylesheets; we can output markup
		 *   to load them.
		 *
		 * Unlike asset imports above, stylesheets are not prefixed with '_',
		 *   so we can link directly to the string provided.
		 */
		if (isset($entry['css']) && !empty($entry['css'])) {
			foreach ($entry['css'] as $stylesheet) {
				?>
				<link rel="stylesheet" href="<?= $this->createAssetURL($stylesheet); ?>">
				<?php
			}
		}
	}


	/* Admin 'back-end' */

	/**
	 * Admin scripts/stylesheets enqueued 'properly' with WordPress.
	 *
	 * TODO
	 */
	private function adminScripts () {
		//
	}

	/**
	 * Admin script/stylesheet markup outputted directly to <head>.
	 */
	private function adminHeadHTML () {
		$this->maybeOutputViteClientMarkup();

		//$this->outputAssetMarkup('resources/scripts/admin.ts');
	}

	/**
	 * Admin script/stylesheet markup outputted directly to end of <body>.
	 *
	 * TODO
	 */
	private function adminFooterHTML () {
		//
	}


	/* Public front-end */

	/**
	 * Public 'front-end' scripts/stylesheets enqueued 'properly' with WordPress.
	 *
	 * TODO
	 */
	private function publicScripts () {
		//
	}

	/**
	 * Public 'front-end' script/stylesheet markup outputted directly to <head>.
	 */
	private function publicHeadHTML () {
		$this->maybeOutputViteClientMarkup();

		$this->outputAssetMarkup('resources/scripts/main.ts');
	}

	/**
	 * Public 'front-end' script/stylesheet markup outputted directly to end of <body>.
	 *
	 * TODO
	 */
	private function publicFooterHTML () {
		//
	}
}
