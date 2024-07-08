<?php

namespace LiquidLight\MediaGallery\Backend;

class ItemsProcFunc
{
	/**
	 * getEngines
	 *
	 * Load the engine names & keys for the TCA/Flexform
	 *
	 * @param array $config
	 *
	 * @return array
	 */
	public function getEngines(array $config): array
	{
		$engines = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['media_gallery']['engines'] ?? [];
		ksort($engines);

		// If there is _only_ one, we don't want the "please select" option
		if (count($engines) !== 1) {
			$config['items'][] = [
				'LLL:EXT:media_gallery/Resources/Private/Language/locallang_tca.xlf:media_gallery.flexform.please_select',
				'',
			];
		}

		foreach ($engines as $key => $value) {
			$config['items'][] = [$value['title'], $key];
		}

		return $config;
	}
}
