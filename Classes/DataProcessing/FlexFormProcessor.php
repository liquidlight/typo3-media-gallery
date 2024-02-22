<?php

namespace LiquidLight\Gallery\DataProcessing;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class FlexFormProcessor implements DataProcessorInterface
{
	/**
	 * @var FlexFormService
	 */
	protected $flexFormService;

	public function __construct()
	{
		$this->flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
	}

	public function process(
		ContentObjectRenderer $cObj,
		array $contentObjectConfiguration,
		array $processorConfiguration,
		array $processedData
	): array {
		$originalValue = $processedData['data']['pi_flexform'];
		if (!is_string($originalValue)) {
			return $processedData;
		}

		$flexformData = $this->flexFormService->convertFlexFormContentToArray($originalValue);
		$processedData['flexform'] = $flexformData;
		return $processedData;
	}
}
