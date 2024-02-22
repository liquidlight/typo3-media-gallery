<?php

namespace LiquidLight\Gallery\ViewHelpers;

use Generator;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Return the copyright license for the passed in file
 *
 * Examples
 * ========
 *
 * Simple
 * ------
 *
 * ::
 *
 *	<nlw:copyright file="{file}" />
 *
 *	{file -> nlw:copyright()}
 *
 * ``CC Attribution-Non Commercial-Share Alike``
 * Depending on the value of ``tx_nlw_copyright`` field on the file.
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
	 *
	 * @return string the copyright notice
	 */
	public static function renderStatic(
		array $arguments,
		\Closure $renderChildrenClosure,
		RenderingContextInterface $renderingContext
	) {
		$config = $arguments['engine'];

		$AssetCollector = GeneralUtility::makeInstance(AssetCollector::class);

		$assetItems = [
			'styleSheet' => 'addStyleSheet',
			'inlineStyleSheet' => 'addInlineStyleSheet',
			'javaScript' => 'addJavaScript',
			'inlineJavaScript' => 'addInlineJavaScript',
		];

		foreach ($assetItems as $key => $method) {
			if (isset($config[$key])) {
				foreach ((array)$config[$key] as $i => $item) {
					$source = is_array($item) ? $item[0] : $item;
					$attributes = is_array($item) && isset($item[1]) ? $item[1] : [];
					$options = is_array($item) && isset($item[2]) ? $item[2] : [];

					$AssetCollector->$method($config['engine'] . $i, $source, $attributes, $options);
				}
			}
		}
	}
}
