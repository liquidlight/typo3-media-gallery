<?php

namespace LiquidLight\Gallery\ViewHelpers;

use Generator;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Adds the assets for the selected engine
 *
 * Examples
 * ========
 *
 * <gallery:assets engine="{engine}" />
 *
 */
class AssetsViewHelper extends AbstractViewHelper
{
	protected $escapeOutput = false;

	public function initializeArguments()
	{
		// Needs an instance of a file
		$this->registerArgument('engine', 'array', 'The engine configuration', true);
	}

	/**
	 * Returns the correctly translated copyright
	 *
	 * @param array $arguments
	 * @param \Closure $renderChildrenClosure
	 * @param RenderingContextInterface $renderingContext
	 */
	public static function renderStatic(
		array $arguments,
		\Closure $renderChildrenClosure,
		RenderingContextInterface $renderingContext
	) {
		// Get the config from LocalConf
		$config = $arguments['engine'];

		// Don't include any assets at all - no point continuing
		if (
			is_null($config) ||
			!count($config) ||
			(isset($config['excludeAllAssets']) && $config['excludeAllAssets'] === true)
		) {
			return;
		}

		// Set up the asset collector
		$AssetCollector = GeneralUtility::makeInstance(AssetCollector::class);

		// Remove any assets not needed
		self::removeConfigurationItems($config, 'excludeAssets', ['styleSheet', 'javaScript']);
		self::removeConfigurationItems($config, 'excludeInlineAssets', ['inlineStyleSheet', 'inlineJavaScript']);

		// Create a map of Gallery keys -> AssetCollector methods
		$assetItems = [
			'styleSheet' => 'addStyleSheet',
			'inlineStyleSheet' => 'addInlineStyleSheet',
			'javaScript' => 'addJavaScript',
			'inlineJavaScript' => 'addInlineJavaScript',
		];

		// Loop through our map above
		foreach ($assetItems as $key => $method) {
			// Check we have it in our config
			if (!isset($config[$key])) {
				continue;
			}

			// If we do, treat it as an array
			foreach ((array)$config[$key] as $i => $item) {
				// Extract each key to allow customisation
				$source = is_array($item) ? $item[0] : $item;
				$attributes = is_array($item) && isset($item[1]) ? $item[1] : [];
				$options = is_array($item) && isset($item[2]) ? $item[2] : [];

				// Include the asset using the method (e.g. `addInlineJavaScript`)
				$AssetCollector->$method(
					// Use the identifier so assets don't get added twice
					$config['identifier'] . $i,
					$source,
					$attributes,
					$options
				);
			}
		}
	}

	/**
	 * removeConfigurationItems
	 *
	 * @param array $config The configuration
	 * @param string $key The array key to use to acknowledge removal
	 * @param array $items The items to remove
	 *
	 * @return void
	 */
	protected static function removeConfigurationItems(array &$config, string $key, array $items): void
	{
		if (isset($config[$key]) && $config[$key] === true) {
			foreach ($items as $item) {
				if (isset($config[$item])) {
					unset($config[$item]);
				}
			}
		}
	}
}
