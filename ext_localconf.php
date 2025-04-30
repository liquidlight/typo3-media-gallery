<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use LiquidLight\MediaGallery\Upgrades\MediaGalleryUpgradeWizard;

(function () {

	ExtensionManagementUtility::addPageTSConfig('
		@import "EXT:media_gallery/Configuration/TSconfig/Page/Mod/Wizards/MediaGallery.tsconfig"
		@import "EXT:media_gallery/Configuration/TSconfig/Page/Mod/Wizards/ContentGallery.tsconfig"
	');

	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['media_gallery']['engines'] = array_merge(
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['media_gallery']['engines'] ?? [],
		[
			'fancybox' => [
				'title' => 'Fancyapps - Fancybox',
				'template' => 'Fancybox',
				'styleSheet' => [
					'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.min.css',
					'EXT:media_gallery/Resources/Public/Css/fancybox.css',
				],
				'javaScript' => 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.umd.min.js',
				'inlineJavaScript' => 'Fancybox.bind("[data-fancybox]");',
			],

			'basic' => [
				'title' => 'Basic - Image List',
				'template' => 'Basic',
				'styleSheet' => 'EXT:media_gallery/Resources/Public/Css/basic.css',
			],

			'carousel' => [
				'title' => 'Fancyapps - Carousel',
				'template' => 'Carousel',
				'styleSheet' => [
					'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/carousel/carousel.css',
					'EXT:media_gallery/Resources/Public/Css/carousel.css',
				],
				'javaScript' => 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/carousel/carousel.umd.js',
				'inlineJavaScript' => '
					const carousels = document.querySelectorAll(".f-carousel"),
						carouselOptions = { adaptiveHeight: true };
					for (const carousel of carousels) {
						new Carousel(carousel, carouselOptions);
					}
				',
			],
		]
	);

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['mediagallery_galleryUpgradeWizard'] = MediaGalleryUpgradeWizard::class;
})();
