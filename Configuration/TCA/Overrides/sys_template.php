<?php

defined('TYPO3_MODE') || die();

call_user_func(function () {
	/**
	 * Steine TypoScript
	 */
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
		'media_gallery',
		'Configuration/TypoScript',
		'Media Gallery'
	);
});
