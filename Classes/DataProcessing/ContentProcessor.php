<?php

namespace LiquidLight\MediaGallery\DataProcessing;

use TYPO3\CMS\Core\Database\RelationHandler;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\ContentObject\RecordsContentObject;

class ContentProcessor implements DataProcessorInterface
{
	private const CONTENT_TABLE = 'tt_content';

	/**
	 * @var FlexFormService
	 */
	protected $flexFormService;

	protected ContentObjectRenderer $cObj;

	protected ?RecordsContentObject $contentRecordRenderer = null;

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
		$this->cObj = $cObj;

		// Add to our output
		$processedData['slides'] = array_map(
			[$this, 'processContentRecord'],
			$this->getDatabaseRecords($cObj->data['records'] ?? [])
		);

		return $processedData;
	}

	protected function getDatabaseRecords(string $items): array
	{
		// Set up the relation handler
		$relationHandler = GeneralUtility::makeInstance(RelationHandler::class);

		// Configure with our details with the TCA as a single source of true
		$relationHandler->start(
			$items,
			$GLOBALS['TCA']['tt_content']['types']['liquidlight_contentgallery']['columnsOverrides']['records']['config']['allowed']
		);
		// Get the items & the rows from the DB
		$relationHandler->getFromDB();

		return $relationHandler->getResolvedItemArray() ?? [];
	}

	/**
	 * Process any records from the tt_content table
	 */
	protected function processContentRecord($record)
	{
		// Render the content element
		$record['renderedContent'] = $this->cObj->render(
			$this->getContentRecordRenderer(),
			[
				'source' => $record['uid'],
				'tables' => $record['table'],
			]
		);

		return $record;
	}

	protected function getContentRecordRenderer(): RecordsContentObject
	{
		if (!$this->contentRecordRenderer) {
			$this->contentRecordRenderer = $this->cObj->getContentObject('RECORDS');
		}

		return $this->contentRecordRenderer;
	}
}
