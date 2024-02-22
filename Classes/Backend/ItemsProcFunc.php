<?php

namespace LiquidLight\Gallery\Backend;

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
		$engines = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['gallery']['engines'] ?? [];
		ksort($engines);

		foreach ($engines as $key => $value) {
			$config['items'][] = [$value['title'], $key];
		}

		return $config;
	}
}
