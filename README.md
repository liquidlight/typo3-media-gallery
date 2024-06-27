# Gallery

A plugin to show images with a list of different engines to display.

## Installation

1. `composer req liquidlight/gallery`
2. Include the TypoScript, either by using static includes or including the files yourself
    - Constants: `@import 'EXT:gallery/Configuration/TypoScript/constants'`
    - Setup: `@import 'EXT:gallery/Configuration/TypoScript/setup'`
3. Add a new gallery to a page & select images
4. Pick your engine (library) or add your own

## Engines

Throughout this extension you will see and interact with the word "engine". This describes the library/package used to render the images. Examples of engines are Fancybox and Swiper. There is also an Ajax and Basic engine included which, by default, adds no extra CSS or JavaScript.

## Configuration

LL Gallery comes bundled with several libraries included, which can all be configured via the `$GLOBALS` array to alter their behaviour. You also have the option of adding your own Gallery engine if desired.


### Remove assets of an existing engine

If an existing or added engine includes assets which you, yourself, are bundling in your site package or FE assets, you can remove assets in various ways.

You can add the following to your `ext_localconf.php`:

- `$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['gallery']['engines']['fancybox']['excludeAllAssets'] = true` - This removes **all** assets added by the engine
- `$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['gallery']['engines']['fancybox']['excludeAssets'] = true` - This removes `stylesheet` and `javascript` assets added to the document
- `$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['gallery']['engines']['fancybox']['excludeInlineAssets'] = true` - This removes any inline assets

Alternatively, you can `unset()` a specific item - e.g.:

```php
unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['gallery']['engines']['fancybox']['styleSheet']);
```

### Add an engine

Engines are set via `$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['gallery']['engines']` and appended to the array.

The key should be a simple/slugified version of the engine/plugin. This allows unsetting and overriding should that be required.

The array can have several keys to add assets & define settings - all of them optional. See below for an explanation of the assets.

#### Adding a new engine

```php
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['gallery']['engines']['engine_key'] = [
    'title' => 'Nice title - shown in the drop down',
    'template' => 'Engine', // The name of the partial used (can be omitted to use the "Basic" one)
    'styleSheet' => '', // CSS to load
    'javaScript' => '', // Load JS
    'inlineStyleSheet' => '', // Any inline CSS
    'inlineJavaScript' => '', // Any inline JS
];
```

#### Assets

The 4 asset keys (`styleSheet`, `javaScript`, `inlineStyleSheet`, `inlineJavaScript`) allow you to load assets when the particular engine is loaded. Each one can take a string or a (multi-dimensional) array and is passed to the TYPO3 `AssetCollector` ([docs](https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ApiOverview/Assets/Index.html#asset-collector)).

Most of the time, the string will be enough, however there may be times where you need to set the priority or add data attributes.

This applies to all 4 assets, but as an example, the CSS can be formatted in 3 different ways:

**As a string**

```php
'styleSheet' => 'https://www.site.com/path/to/css.css'
```

**As an array**

```php
'styleSheet' => [
    'https://www.site.com/path/to/css.css',
    'https://www.differentsite.com/path/to/css.css',
]
```

**As a multidimensional array**

This allows you to set attributes and options as you would if using the `AssetCollector` directly

```php
'styleSheet' => [
    [
        'https://www.site.com/path/to/css.css',
        ['data-foo' => 'bar'], // $attributes
        ['priority' => true], // $options
    ]
]
```

## Templates

If you wish to override any [templates](/Resources/Private/Templates/) or [partials](/Resources/Private/Partials/), add the following to your `constants.typoscript`.

```
site {
	fluidtemplate {
		gallery {
			partialRootPath = EXT:###YOUR EXT###/Resources/Private/Partials/Gallery/
			templateRootPath = EXT:###YOUR EXT###/Resources/Private/Templates/Gallery/
		}
	}
}
```

Alternatively, you can add them directly to the [`setup.typoscript`](/Configuration/TypoScript/setup.typoscript)

**Note**: The custom `<gallery:assets engine="{engine}" />` view helper is how the assets are injected into the template - ensure your template contains this if overriding the `Gallery.html`

## Upgrading

When upgrading from v3 (our internal, private package) to v4, please do the following:

- Install the new gallery
- Run `typo3cms upgrade:prepare` and `typo3cms upgrade:run gallery_galleryUpgradeWizard`
- Include the TypoScript, either via a static include or including it in your application:
    - Constants: `@import 'EXT:gallery/Configuration/TypoScript/constants'`
    - Setup: `@import 'EXT:gallery/Configuration/TypoScript/setup'`
- Add template overrides to match the existing gallery
- You may need to unset some assets if libraries are already loaded (e.g. Fancybox)
- Uninstall the old gallery
