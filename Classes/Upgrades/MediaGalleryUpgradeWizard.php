<?php

declare(strict_types=1);

namespace LiquidLight\MediaGallery\Upgrades;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\ReferenceIndexUpdatedPrerequisite;

/**
 * ll_gallery to gallery conversion
 *
 * Upgrades plugins using a legacy ll_gallery plugin to the new gallery
 *
 * @author Mike Street <mike@liquidlight.co.uk>
 * @copyright Liquid Light Ltd.
 * @package TYPO3
 * @subpackage gallery
 */
class MediaGalleryUpgradeWizard implements UpgradeWizardInterface
{
	/**
	 * @var FlexFormService
	 */
	protected $flexFormService;

	/**
	 * @var QueryBuilder
	 */
	protected $queryBuilder;

	public const TABLE_NAME = 'tt_content';

	public function __construct()
	{
		$this->flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
		$this->queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
			->getQueryBuilderForTable(static::TABLE_NAME)
		;
	}

	/**
	 * Return the identifier for this wizard
	 * This should be the same string as used in the ext_localconf class registration
	 *
	 * @return string
	 */
	public function getIdentifier(): string
	{
		return 'mediagallery_galleryUpgradeWizard';
	}

	/**
	 * Return the speaking name of this wizard
	 *
	 * @return string
	 */
	public function getTitle(): string
	{
		return 'll_gallery to gallery conversion';
	}

	/**
	 * Return the description for this wizard
	 *
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Upgrades plugins using a legacy ll_gallery plugin to the new gallery';
	}

	/**
	 * Execute the update
	 *
	 * Called when a wizard reports that an update is necessary
	 *
	 * @return bool
	 */
	public function executeUpdate(): bool
	{
		$legacy = $this->getLegacyGalleryPlugins();

		foreach ($legacy as $item) {
			$flexformData = $this->flexFormService->convertFlexFormContentToArray($item['pi_flexform']);

			if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['media_gallery']['engines'][$flexformData['engine']])) {
				echo $flexformData['engine'] . ' is not supported, skipping: ' . $item['uid'];
			}

			if ($flexformData['source'] == 'records') {
				$this->updateIndividualImageRecords($item);
			} else {
				$collection = $this->createFileCollectionFromFolder($item, $flexformData);
				$this->updateFileCollection($item, $collection);
			}

			$this->updateItemType($item, $flexformData['engine']);
		}

		return true;
	}

	/**
	 * Is an update necessary?
	 *
	 * Is used to determine whether a wizard needs to be run.
	 * Check if data for migration exists.
	 *
	 * @return bool Whether an update is required (TRUE) or not (FALSE)
	 */
	public function updateNecessary(): bool
	{
		return count($this->getLegacyGalleryPlugins()) > 0;
	}

	/**
	 * Returns an array of class names of prerequisite classes
	 *
	 * This way a wizard can define dependencies like "database up-to-date" or
	 * "reference index updated"
	 *
	 * @return string[]
	 */
	public function getPrerequisites(): array
	{
		return [
			DatabaseUpdatedPrerequisite::class,
			ReferenceIndexUpdatedPrerequisite::class,
		];
	}

	/**
	 * getLegacyGalleryPlugins
	 *
	 * Are there any plugins to be converted?
	 *
	 * @return array
	 */
	protected function getLegacyGalleryPlugins(): array
	{
		return $this->queryBuilder
			->select('*')
			->from(static::TABLE_NAME)
			->where(
				$this->queryBuilder->expr()->eq(
					'list_type',
					$this->queryBuilder->createNamedParameter('ll_gallery_pi')
				),
				$this->queryBuilder->expr()->eq(
					'CType',
					$this->queryBuilder->createNamedParameter('list')
				)
			)
			->execute()
			->fetchAllAssociative()
		;
	}

	/**
	 * updateItemType
	 *
	 * Update the tt_content and set the engine
	 *
	 * @param array $item
	 * @param string $engine
	 *
	 * @return void
	 */
	protected function updateItemType(array $item, string $engine): void
	{
		$this->queryBuilder
			->update('tt_content')
			->where(
				$this->queryBuilder->expr()->eq('uid', $this->queryBuilder->createNamedParameter($item['uid']))
			)
			->set('CType', 'liquidlight_mediagallery')
			->set('list_type', '')
			->set('pi_flexform', '
				<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
				<T3FlexForms>
					<data>
						<sheet index="sDEF">
							<language index="lDEF">
								<field index="engine">
									<value index="vDEF">' . $engine . '</value>
								</field>
							</language>
						</sheet>
					</data>
				</T3FlexForms>
			')
			->execute()
		;
	}

	/**
	 * updateIndividualImageRecords
	 *
	 * Move the images from flexform ll_gallery to "media"
	 *
	 * @param array $item
	 *
	 * @return void
	 */
	protected function updateIndividualImageRecords(array $item): void
	{
		$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
			->getQueryBuilderForTable('sys_file_reference')
		;

		$queryBuilder
			->update('sys_file_reference')
			->where(
				$queryBuilder->expr()->eq('uid_foreign', $queryBuilder->createNamedParameter($item['uid'])),
				$queryBuilder->expr()->eq('fieldname', $queryBuilder->createNamedParameter('ll_gallery'))
			)
			->set('fieldname', 'media')
			->execute()
		;
	}

	/**
	 * createFileCollectionFromFolder
	 *
	 * Create a file collection of a folder
	 *
	 * @param array $item
	 * @param array $flexformData
	 *
	 * @return int
	 */
	protected function createFileCollectionFromFolder(array $item, array $flexformData): int
	{
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('sys_file_collection');

		// Does it exist already?
		$existing = $queryBuilder
			->select('uid')
			->from('sys_file_collection')
			->where(
				$queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($item['pid'])),
				$queryBuilder->expr()->eq('type', $queryBuilder->createNamedParameter('folder')),
				$queryBuilder->expr()->eq('folder', $queryBuilder->createNamedParameter($flexformData['folder']))
			)
			->execute()
			->fetchAllAssociative()
		;

		if (count($existing)) {
			return $existing[0];
		}

		$queryBuilder
			->insert('sys_file_collection')
			->values([
				'pid' => (int)$item['pid'],
				'type' => 'folder',
				'title' => $flexformData['folder'],
				'folder' => $flexformData['folder'],
				'storage' => (int)$flexformData['storage'],
			])
			->execute()
		;

		return (int)$connectionPool->getConnectionForTable('sys_file_collection')->lastInsertId('sys_file_collection');
	}

	/**
	 * updateFileCollection
	 *
	 * Set the file collection on a Gallery item
	 *
	 * @param array $item
	 * @param int $collection
	 *
	 * @return void
	 */
	protected function updateFileCollection(array $item, int $collection): void
	{
		$this->queryBuilder
			->update('tt_content')
			->where(
				$this->queryBuilder->expr()->eq('uid', $this->queryBuilder->createNamedParameter($item['uid']))
			)
			->set('file_collections', (string)$collection)
			->execute()
		;
	}
}
