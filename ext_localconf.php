<?php

defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
	'@import "EXT:gallery/Configuration/TSconfig/Page/Mod/Wizards/Gallery.tsconfig"'
);

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['gallery']['engines'] = array_merge(
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['gallery']['engines'] ?? [],
	[
		'fancybox' => [
			'title' => 'Fancyapps - Fancybox',
			'template' => 'Fancybox',
			'styleSheet' => 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.min.css',
			'javaScript' => 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.umd.min.js',
			'inlineJavaScript' => 'Fancybox.bind("[data-fancybox]");'
		],
		'basic' => [
			'title' => 'Basic - Image List',
			'template' => 'Basic',
			'styleSheet' => 'EXT:gallery/Resources/Public/Css/basic.css'
		],
		'carousel' => [
			'title' => 'Fancyapps - Carousel',
			'template' => 'Carousel',
			'styleSheet' => [
				'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/carousel/carousel.css',
				'EXT:gallery/Resources/Public/Css/carousel.css'
			],
			'javaScript' => 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/carousel/carousel.umd.js',
			'inlineJavaScript' => '
				const carousels = document.querySelectorAll(".f-carousel"),
					carouselOptions = { adaptiveHeight: true };
				for (const carousel of carousels) {
					new Carousel(carousel, carouselOptions);
				}
			'
		],
	]
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['gallery_galleryUpgradeWizard']
	= \LiquidLight\Gallery\Upgrades\GalleryUpgradeWizard::class;
