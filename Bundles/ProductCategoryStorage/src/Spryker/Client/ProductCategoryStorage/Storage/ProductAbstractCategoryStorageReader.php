<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductCategoryStorage\Storage;

use Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer;
use Generated\Shared\Transfer\SynchronizationDataTransfer;
use Spryker\Client\Kernel\Locator;
use Spryker\Client\ProductCategoryStorage\Dependency\Client\ProductCategoryStorageToStorageClientInterface;
use Spryker\Client\ProductCategoryStorage\Dependency\Service\ProductCategoryStorageToSynchronizationServiceInterface;
use Spryker\Client\ProductCategoryStorage\ProductCategoryStorageConfig;
use Spryker\Shared\ProductCategoryStorage\ProductCategoryStorageConfig as SharedProductCategoryStorageConfig;

class ProductAbstractCategoryStorageReader implements ProductAbstractCategoryStorageReaderInterface
{
    /**
     * @var \Spryker\Client\ProductCategoryStorage\Dependency\Client\ProductCategoryStorageToStorageClientInterface
     */
    protected $storageClient;

    /**
     * @var \Spryker\Client\ProductCategoryStorage\Dependency\Service\ProductCategoryStorageToSynchronizationServiceInterface
     */
    protected $synchronizationService;

    /**
     * @param \Spryker\Client\ProductCategoryStorage\Dependency\Client\ProductCategoryStorageToStorageClientInterface $storageClient
     * @param \Spryker\Client\ProductCategoryStorage\Dependency\Service\ProductCategoryStorageToSynchronizationServiceInterface $synchronizationService
     */
    public function __construct(
        ProductCategoryStorageToStorageClientInterface $storageClient,
        ProductCategoryStorageToSynchronizationServiceInterface $synchronizationService
    ) {
        $this->storageClient = $storageClient;
        $this->synchronizationService = $synchronizationService;
    }

    /**
     * @param int $idProductAbstract
     * @param string $localeName
     * @param string $storeName
     *
     * @return \Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer|null
     */
    public function findProductAbstractCategory(
        int $idProductAbstract,
        string $localeName,
        string $storeName
    ): ?ProductAbstractCategoryStorageTransfer {
        $productAbstractCategoryStorageData = $this->findStorageData($idProductAbstract, $localeName, $storeName);

        if (!$productAbstractCategoryStorageData) {
            return null;
        }

        $spyProductCategoryAbstractTransfer = new ProductAbstractCategoryStorageTransfer();

        return $spyProductCategoryAbstractTransfer->fromArray($productAbstractCategoryStorageData, true);
    }

    /**
     * @param array<int> $productAbstractIds
     * @param string $localeName
     * @param string $storeName
     *
     * @return array<\Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer>
     */
    public function findBulkProductAbstractCategory(array $productAbstractIds, string $localeName, string $storeName): array
    {
        $productAbstractCategoryStorageData = $this->findBulkStorageData($productAbstractIds, $localeName, $storeName);
        $productAbstractCategoryStorageData = array_filter($productAbstractCategoryStorageData);

        if (!$productAbstractCategoryStorageData) {
            return [];
        }

        $response = [];
        foreach ($productAbstractCategoryStorageData as $item) {
            $response[] = (new ProductAbstractCategoryStorageTransfer())
                ->fromArray($item, true);
        }

        return $response;
    }

    /**
     * @param int $idProductAbstract
     * @param string $localeName
     * @param string $storeName
     *
     * @return array|null
     */
    protected function findStorageData(int $idProductAbstract, string $localeName, string $storeName): ?array
    {
        if (ProductCategoryStorageConfig::isCollectorCompatibilityMode()) {
            $clientLocatorClass = Locator::class;
            /** @var \Generated\Zed\Ide\AutoCompletion&\Spryker\Shared\Kernel\LocatorLocatorInterface $locator */
            $locator = $clientLocatorClass::getInstance();
            $productClient = $locator->product()->client();
            // TODO: $storeName should be used here too.
            $collectorData = $productClient->getProductAbstractFromStorageById($idProductAbstract, $localeName);
            $categories = [];

            foreach ($collectorData['categories'] as $category) {
                $categories[] = [
                    'category_node_id' => $category['nodeId'],
                    'name' => $category['name'],
                    'url' => $category['url'],
                ];
            }

            return [
                'id_product_abstract' => $idProductAbstract,
                'categories' => $categories,
            ];
        }

        $key = $this->generateKey((string)$idProductAbstract, $localeName, $storeName);

        return $this->storageClient->get($key);
    }

    /**
     * @param array<int> $productAbstractIds
     * @param string $localeName
     * @param string $storeName
     *
     * @return array
     */
    protected function findBulkStorageData(array $productAbstractIds, string $localeName, string $storeName): array
    {
        $storageKeys = [];
        foreach ($productAbstractIds as $idProductAbstract) {
            $storageKeys[] = $this->generateKey((string)$idProductAbstract, $localeName, $storeName);
        }

        $productAbstractCategoryStorageData = $this->storageClient->getMulti($storageKeys);

        $decodedProductAbstractCategoryStorageData = [];
        foreach ($productAbstractCategoryStorageData as $item) {
            $decodedProductAbstractCategoryStorageData[] = json_decode($item, true);
        }

        return $decodedProductAbstractCategoryStorageData;
    }

    /**
     * @param string $idProductAbstract
     * @param string $localeName
     * @param string $storeName
     *
     * @return string
     */
    protected function generateKey(string $idProductAbstract, string $localeName, string $storeName): string
    {
        $synchronizationDataTransfer = new SynchronizationDataTransfer();
        $synchronizationDataTransfer
            ->setLocale($localeName)
            ->setStore($storeName)
            ->setReference($idProductAbstract);

        return $this->synchronizationService
            ->getStorageKeyBuilder(SharedProductCategoryStorageConfig::PRODUCT_ABSTRACT_CATEGORY_RESOURCE_NAME)
            ->generateKey($synchronizationDataTransfer);
    }
}
