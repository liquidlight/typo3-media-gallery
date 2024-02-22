<?php

defined('TYPO3_MODE') || die();

call_user_func(function () {
	/**
	 * Steine TypoScript
	 */
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
		'gallery',
		'Configuration/TypoScript',
		'Gallery'
	);
});
