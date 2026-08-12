<?php

defined('TYPO3') or die();
call_user_func(function () {
	// Adds the content element to the "Type" dropdown
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
		'tt_content',
		'CType',
		[
			'LLL:EXT:media_gallery/Resources/Private/Language/locallang_tca.xlf:content_gallery',
			'liquidlight_contentgallery',
			'apps-clipboard-images',
		],
		'uploads',
		'after'
	);

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
		'*',
		'FILE:EXT:media_gallery/Configuration/FlexForms/ContentGallery.xml',
		'liquidlight_contentgallery',
	);


	// Configure the default backend fields for the content element
	$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['liquidlight_contentgallery'] = 'apps-clipboard-images';
	$GLOBALS['TCA']['tt_content']['types']['liquidlight_contentgallery'] = [
		'showitem' => '
			--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
				--palette--;;general,
				--palette--;;headers,
				pi_flexform,
				records,
			--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
				--palette--;;frames,
				--palette--;;appearanceLinks,
			--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
				--palette--;;language,
			--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
				--palette--;;hidden,
				--palette--;;access,
			--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
				categories,
			--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
				rowDescription,
			--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
		',
	];

	$GLOBALS['TCA']['tt_content']['types']['liquidlight_contentgallery']['columnsOverrides'] = [
		'records' => [
			'config' => [
				'allowed' => 'tt_content',
			],
		],
	];

});
