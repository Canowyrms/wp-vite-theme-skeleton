# WordPress Theme Skeleton with Vite Asset Bundling

This theme skeleton is very much a work-in-progress, but it should be enough to get you off the ground using Vite to compile assets in a WordPress project.

With some minor modifications, this skeleton can be used in a plugin, too.

This was slapped together in somewhat of a rush - don't be surprised if best-practices weren't followed.


## Requirements

- PHP 8.0+
- Composer
- [Volta](https://volta.sh/) - Not a hard requirement, but can be used to manage Node and pnpm versions.
- Node.js LTS (some recent version; I'm using v20.9.0)
- pnpm (some recent version; I'm using v8.15.4. npm probably works too)


## Setup

1. Update `style.css` with a proper theme name and, if applicable, template (parent theme) directive.
2. Update `functions.php` and `src/controllers/AssetController.php` with proper `namespace` directive.
3. Update `vite.config.ts` `projectPath` variable.
	- The path should be relative to your project's web-root. For Bedrock projects, this should be `/app/themes/THEME_NAME/`.
4. Update `vite.config.ts` and `src/controllers/AssetController.php` with entry-points.
	- By default, `admin.ts` is disabled; uncomment if using assets in admin dashboard.
	- At the time of writing, the preferred way of loading Vite assets is by outputting markup directly onto the page. See the `admin`/`public`-`Head`/`Footer`-`HTML` methods.
5. `pnpm i` (yes, that's **p**npm, _not_ npm)


## Directories Explained

- **`assets/`** - Compiled assets live here. Never put anything here manually. Your changes will likely be lost next time you build.

- **`resources/`** - Asset source-code live here.

- **`resources/fonts/`** - Font files go here. **NOTE**: See known issue below.

- **`resources/images/`** - Images of any format go here. **NOTE**: See known issue below.

- **`resources/scripts/`** - Any JavaScript/TypeScript source files live here. The theme comes with `main.ts`, intended for the public-facing 'front-end', and `admin.ts`, intended for the admin 'back-end'.

- **`resources/static/`** - For anything that doesn't fit into the other directories. Assets here are copied to the build dir without any further processing. 
	- Do note that if you include an asset from this directory in a script or stylesheet, the asset will be copied AND processed, resulting in a duplication, which you probably don't want.
	- Further note that you can just create another directory in `resources/` that suits your needs.

- **`resources/styles/`** - Any stylesheet source files live here. The theme comes with `main.scss`, intended for the public-facing 'front-end', and `admin.scss`, intended for the admin 'back-end'.


## Known Issues

### Vite and relative paths in sass/scss

https://github.com/vitejs/vite/issues/11012

TLDR: Using relative file paths inside nested sass/scss files fails transformation during build.

Workaround:

Depending on how angry you want your editor to be with you, you have two options that both seem to work in my limited testing:

1. Aliases in `vite.config.js` - The `@fonts` and `@images` aliases reference the `resources/fonts` and `resources/images` directories respectively. You can use these inside URL strings in any sass/scss file, nested or otherwise. Unfortunately, my editor (PhpStorm) gets angry about this.
	- Example usage: `background: url('@images/myimage.png');`
2. Variables in `main.scss`/`admin.scss` - The `$fonts` and `$images` variables reference the `resources/fonts` and `resources/images` directories respectively. You concatenate your asset to the variable to create URL strings. Works in nested files. My editor is less angry about this one; it just doesn't know how to resolve the path.
	- Example usage: `background: ($images + '/myimage.png');`

This theme skeleton is prepped with both options.


### PhpStorm freaks out about `vite.config.ts`

Naturally, regarding the issue above, PhpStorm has something _else_ to complain about. It freaks out about `import.meta.url` in the `alias` object. Perhaps this is solved in newer versions; I'm still on 2020.3 :').

Workaround:

- Open File -> Settings -> Languages & Frameworks -> Typescript
- Add `--module=esnext` to 'Options' field.


## TODOs

Oh man. So many. Search for "TODO" in this directory.
