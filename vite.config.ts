import { defineConfig } from 'vite';
import { liveReload } from 'vite-plugin-live-reload';
import { resolve } from 'path';
import { fileURLToPath } from 'url';

let liveReloadPaths = [
	__dirname + '/**/*.php',
];

let liveReloadOptions = {
	alwaysReload: true,
	root: process.cwd(),
};

let projectPath = '/path/to/theme/' + 'assets/';

export default defineConfig({
	root: '',

	//@fmt:off
	base: process.env.NODE_ENV === 'development'
		  // In dev mode, assets are relative to Vite's webroot.
	      ? '/'
		  // For builds, asset paths are relative to the project's webroot.
	      : projectPath,
	//@fmt:on

	// Relative to `root`
	publicDir: 'resources/static',

	plugins: [
		liveReload(liveReloadPaths, liveReloadOptions),
	],

	resolve: {
		/*
		// TODO - Use this in conjunction with tsconfig's `paths` ?
		// TODO - Old format?
		alias: [
			{
				find: '@components',
				// TODO - maybe ./resources/scripts/components ?
				replacement: resolve(__dirname, 'resources/scripts/components')
			},
			{
				find: '@views', 
				// TODO - maybe ./resources/scripts/views ?
				replacement: resolve(__dirname, 'resources/scripts/views')
			},
		],
		*/
		alias: {
			/*
			// TODO - New format?
			//@fmt:off
			'@components': resolve(__dirname, 'resources/scripts/components'),
			'@views':      resolve(__dirname, 'resources/scripts/views'),
			//@fmt:on
			*/

			/**
			 * Relative paths don't work properly in nested SCSS partials. The build step
			 *   fails to transform the path and will fail to optimize the asset.
			 *
			 * Use
			 *   `@images/<image name>` for images, and
			 *   `@fonts/<font name>` for fonts.
			 *
			 * @see https://github.com/vitejs/vite/issues/11012
			 */
			//@fmt:off
			'@fonts':  fileURLToPath(new URL('./resources/fonts',  import.meta.url)),
			'@images': fileURLToPath(new URL('./resources/images', import.meta.url)),
			//@fmt:on
		},
	},

	build: {
		// Relative to `root`.
		outDir: 'assets',

		// Relative to `outDir`.
		assetsDir: '',

		// Wipe build dir on every build.
		emptyOutDir: true,

		// Output asset manifest. We'll use it on the PHP side.
		manifest: true,

		// Compatibility transform target.
		target: 'esnext',

		// Script entrypoints.
		rollupOptions: {
			input: [
				'resources/scripts/main.ts',
				//'resources/scripts/admin.ts',
			],
		}
	},

	server: {
		// Required to load scripts from custom host.
		cors: true,

		//port: 3000,
		port: 5173,

		// We need a strict port to match on PHP side.
		strictPort: true,

		// Serve over HTTP
		https: false,

		// TODO - This whole thing.
		// Serve over HTTPS
		// to generate localhost certificate follow the link:
		// https://github.com/FiloSottile/mkcert - Windows, MacOS and Linux supported - Browsers Chrome, Chromium and Firefox (FF MacOS and Linux only)
		// installation example on Windows 10:
		// > choco install mkcert (this will install mkcert)
		// > mkcert -install (global one time install)
		// > mkcert localhost (in project folder files localhost-key.pem & localhost.pem will be created)
		// uncomment below to enable https
		/*
		https: {
		 key: fs.readFileSync('localhost-key.pem'),
		 cert: fs.readFileSync('localhost.pem'),
		},
		*/

		hmr: {
			host: 'localhost',
			// If using HTTPS
			//port: 443
		},
	},
});