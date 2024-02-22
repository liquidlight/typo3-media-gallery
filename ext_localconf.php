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
		],
		'basic' => [
			'title' => 'Basic - Image List',
			'template' => 'Basic',
		],
	]
);


// // Register the default engines
// tx_gallery::registerEngine('Cycle', 'cycle');
// tx_gallery::registerEngine('Galleria', 'galleria');
// tx_gallery::registerEngine('Galleriffic', 'galleriffic');
// tx_gallery::registerEngine('GalleryView', 'galleryview');
// tx_gallery::registerEngine('PrettyPhoto', 'prettyphoto');
// tx_gallery::registerEngine('Fancybox', 'fancybox');
// tx_gallery::registerEngine('Slimbox', 'slimbox');
// tx_gallery::registerEngine('Infinite Carousel', 'infinitecarousel');
// tx_gallery::registerEngine('RoyalSlider', 'royalslider');
