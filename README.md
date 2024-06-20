# Gallery

A plugin to show images with a list of different engines to display.

## Engines

Throughout this extension you will see and interact with the word "engine". This describes the library/package used to render the images. Examples of engines are Fancybox and Swiper. There is also an Ajax and Basic engine included which, by default, adds no extra CSS or JavaScript.

## Configuration

LL Gallery comes bundled with several libraries included, which can all be configured via the `$GLOBALS` array to alter their behaviour. You also have the option of adding your own Gallery engine if desired.

### Add/edit an engine

Engines are set via `$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['gallery']['engines']` and appended to the array.

The key should be a simple/slugified version of the engine/plugin. This allows unsetting and overriding should that be required.

The array can have several keys to add assets & define settings - all of them optional. See below for an explanation of the assets.

#### Example

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


## Upgrading

When upgrading from v3 (our internal, private package) to v4, please do the following:

- Install the new gallery
- Run `typo3cms upgrade:prepare` and `typo3cms upgrade:run gallery_galleryUpgradeWizard`
- Include the TypoScript, either via a static include or including it in your application:
    - Constants: `@import 'EXT:gallery/Configuration/TypoScript/constants'`
    - Setup: `@import 'EXT:gallery/Configuration/TypoScript/setup'`
- Add template overrides to match the existing gallery
- Uninstall the old gallery