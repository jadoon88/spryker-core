<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductPageSearch\Business\Publisher;

use Generated\Shared\Transfer\ProductPageLoadTransfer;
use Generated\Shared\Transfer\ProductPageSearchTransfer;
use Generated\Shared\Transfer\ProductPayloadTransfer;
use Orm\Zed\ProductPageSearch\Persistence\SpyProductAbstractPageSearch;
use Spryker\Shared\ProductPageSearch\ProductPageSearchConfig as SharedProductPageSearchConfig;
use Spryker\Zed\Kernel\Persistence\EntityManager\InstancePoolingTrait;
use Spryker\Zed\ProductPageSearch\Business\Exception\PluginNotFoundException;
use Spryker\Zed\ProductPageSearch\Business\Mapper\ProductPageSearchMapperInterface;
use Spryker\Zed\ProductPageSearch\Business\Model\ProductPageSearchWriterInterface;
use Spryker\Zed\ProductPageSearch\Business\Reader\AddToCartSkuReaderInterface;
use Spryker\Zed\ProductPageSearch\Dependency\Facade\ProductPageSearchToStoreFacadeInterface;
use Spryker\Zed\ProductPageSearch\Persistence\ProductPageSearchQueryContainerInterface;
use Spryker\Zed\ProductPageSearch\ProductPageSearchConfig;

class ProductAbstractPagePublisher implements ProductAbstractPagePublisherInterface
{
    use InstancePoolingTrait;

    /**
     * @var string
     */
    public const PRODUCT_ABSTRACT_LOCALIZED_ENTITY = 'PRODUCT_ABSTRACT_LOCALIZED_ENTITY';

    /**
     * @var string
     */
    public const PRODUCT_ABSTRACT_PAGE_SEARCH_ENTITY = 'PRODUCT_ABSTRACT_PAGE_SEARCH_ENTITY';

    /**
     * @var string
     */
    public const STORE_NAME = 'STORE_NAME';

    /**
     * @var string
     */
    public const LOCALE_NAME = 'LOCALE_NAME';

    /**
     * @var \Spryker\Zed\ProductPageSearch\Persistence\ProductPageSearchQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var array<\Spryker\Zed\ProductPageSearchExtension\Dependency\Plugin\ProductPageDataLoaderPluginInterface>
     */
    protected $productPageDataLoaderPlugins = [];

    /**
     * @var array<\Spryker\Zed\ProductPageSearch\Dependency\Plugin\ProductPageDataExpanderInterface>
     */
    protected $pageDataExpanderPlugins = [];

    /**
     * @var array<\Spryker\Zed\ProductPageSearchExtension\Dependency\Plugin\ProductPageSearchCollectionFilterPluginInterface>
     */
    protected $productPageSearchCollectionFilterPlugins = [];

    /**
     * @var \Spryker\Zed\ProductPageSearch\Business\Mapper\ProductPageSearchMapperInterface
     */
    protected $productPageSearchMapper;

    /**
     * @var \Spryker\Zed\ProductPageSearch\Business\Model\ProductPageSearchWriterInterface
     */
    protected $productPageSearchWriter;

    /**
     * @var \Spryker\Zed\ProductPageSearch\Dependency\Facade\ProductPageSearchToStoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @var \Spryker\Zed\ProductPageSearch\ProductPageSearchConfig
     */
    protected $productPageSearchConfig;

    /**
     * @var \Spryker\Zed\ProductPageSearch\Business\Reader\AddToCartSkuReaderInterface
     */
    protected $addToCartSkuReader;

    /**
     * @param \Spryker\Zed\ProductPageSearch\Persistence\ProductPageSearchQueryContainerInterface $queryContainer
     * @param array<\Spryker\Zed\ProductPageSearch\Dependency\Plugin\ProductPageDataExpanderInterface> $pageDataExpanderPlugins
     * @param array<\Spryker\Zed\ProductPageSearchExtension\Dependency\Plugin\ProductPageDataLoaderPluginInterface> $productPageDataLoaderPlugins
     * @param array<\Spryker\Zed\ProductPageSearchExtension\Dependency\Plugin\ProductPageSearchCollectionFilterPluginInterface> $productPageSearchCollectionFilterPlugins
     * @param \Spryker\Zed\ProductPageSearch\Business\Mapper\ProductPageSearchMapperInterface $productPageSearchMapper
     * @param \Spryker\Zed\ProductPageSearch\Business\Model\ProductPageSearchWriterInterface $productPageSearchWriter
     * @param \Spryker\Zed\ProductPageSearch\ProductPageSearchConfig $productPageSearchConfig
     * @param \Spryker\Zed\ProductPageSearch\Dependency\Facade\ProductPageSearchToStoreFacadeInterface $storeFacade
     * @param \Spryker\Zed\ProductPageSearch\Business\Reader\AddToCartSkuReaderInterface $addToCartSkuReader
     */
    public function __construct(
        ProductPageSearchQueryContainerInterface $queryContainer,
        array $pageDataExpanderPlugins,
        array $productPageDataLoaderPlugins,
        array $productPageSearchCollectionFilterPlugins,
        ProductPageSearchMapperInterface $productPageSearchMapper,
        ProductPageSearchWriterInterface $productPageSearchWriter,
        ProductPageSearchConfig $productPageSearchConfig,
        ProductPageSearchToStoreFacadeInterface $storeFacade,
        AddToCartSkuReaderInterface $addToCartSkuReader
    ) {
        $this->queryContainer = $queryContainer;
        $this->pageDataExpanderPlugins = $pageDataExpanderPlugins;
        $this->productPageDataLoaderPlugins = $productPageDataLoaderPlugins;
        $this->productPageSearchCollectionFilterPlugins = $productPageSearchCollectionFilterPlugins;
        $this->productPageSearchMapper = $productPageSearchMapper;
        $this->productPageSearchWriter = $productPageSearchWriter;
        $this->productPageSearchConfig = $productPageSearchConfig;
        $this->storeFacade = $storeFacade;
        $this->addToCartSkuReader = $addToCartSkuReader;
    }

    /**
     * @param array<int> $productAbstractIds
     *
     * @return void
     */
    public function publish(array $productAbstractIds)
    {
        $productAbstractIdsChunks = array_chunk(
            $productAbstractIds,
            $this->productPageSearchConfig->getProductAbstractPagePublishChunkSize(),
        );

        foreach ($productAbstractIdsChunks as $productAbstractIdsChunk) {
            $this->publishEntities($productAbstractIdsChunk, [], false);
        }
    }

    /**
     * @param array<int> $productAbstractIds
     * @param array<string> $pageDataExpanderPluginNames
     *
     * @return void
     */
    public function refresh(array $productAbstractIds, array $pageDataExpanderPluginNames = [])
    {
        $isPoolingStateChanged = $this->disableInstancePooling();

        $productAbstractIdsChunks = array_chunk(
            array_unique($productAbstractIds),
            $this->productPageSearchConfig->getProductAbstractPagePublishChunkSize(),
        );

        foreach ($productAbstractIdsChunks as $productAbstractIdsChunk) {
            $this->publishEntities($productAbstractIdsChunk, $pageDataExpanderPluginNames, true);
        }

        if ($isPoolingStateChanged) {
            $this->enableInstancePooling();
        }
    }

    /**
     * @param array<int> $productAbstractIds
     *
     * @return void
     */
    public function unpublish(array $productAbstractIds)
    {
        $productAbstractPageSearchEntities = $this->findProductAbstractPageSearchEntities($productAbstractIds);

        $this->deleteProductAbstractPageSearchEntities($productAbstractPageSearchEntities);
    }

    /**
     * @param array<\Orm\Zed\ProductPageSearch\Persistence\SpyProductAbstractPageSearch> $productAbstractPageSearchEntities
     *
     * @return void
     */
    protected function deleteProductAbstractPageSearchEntities(array $productAbstractPageSearchEntities)
    {
        foreach ($productAbstractPageSearchEntities as $productAbstractPageSearchEntity) {
            $productAbstractPageSearchEntity->delete();
        }
    }

    /**
     * @param array<int> $productAbstractIds
     * @param array<string> $pageDataExpanderPluginNames
     * @param bool $isRefresh
     *
     * @return void
     */
    protected function publishEntities(array $productAbstractIds, array $pageDataExpanderPluginNames, $isRefresh = false)
    {
        $pageDataExpanderPlugins = $this->getPageDataExpanderPlugins($pageDataExpanderPluginNames);

        $payloadTransfers = [];
        foreach ($productAbstractIds as $productAbstractId) {
            $payloadTransfers[$productAbstractId] = (new ProductPayloadTransfer())->setIdProductAbstract($productAbstractId);
        }

        $productPageLoadTransfer = (new ProductPageLoadTransfer())
            ->setProductAbstractIds($productAbstractIds)
            ->setPayloadTransfers($payloadTransfers);

        foreach ($this->productPageDataLoaderPlugins as $productPageDataLoaderPlugin) {
            $productPageLoadTransfer = $productPageDataLoaderPlugin->expandProductPageDataTransfer($productPageLoadTransfer);
        }

        $productAbstractLocalizedEntities = $this->findProductAbstractLocalizedEntities($productAbstractIds);
        $productCategories = $this->getProductCategoriesByProductAbstractIds($productAbstractIds);
        $productAbstractLocalizedEntities = $this->hydrateProductAbstractLocalizedEntitiesWithProductCategories($productCategories, $productAbstractLocalizedEntities);

        if ($this->productPageSearchConfig->isProductAbstractAddToCartEnabled()) {
            $productAbstractLocalizedEntities = $this->hydrateProductAbstractLocalizedEntitiesWithProductAbstractAddToCartSku(
                $productAbstractLocalizedEntities,
                $productAbstractIds,
            );
        }

        $productAbstractPageSearchEntities = $this->findProductAbstractPageSearchEntities($productAbstractIds);
        if (!$productAbstractLocalizedEntities) {
            $this->deleteProductAbstractPageSearchEntities($productAbstractPageSearchEntities);

            return;
        }

        $this->storeData(
            $productAbstractLocalizedEntities,
            $productAbstractPageSearchEntities,
            $pageDataExpanderPlugins,
            $productPageLoadTransfer,
            $isRefresh,
        );
    }

    /**
     * @param array<array<string, mixed>> $productAbstractLocalizedEntities
     * @param array<\Orm\Zed\ProductPageSearch\Persistence\SpyProductAbstractPageSearch> $productAbstractPageSearchEntities
     * @param array<\Spryker\Zed\ProductPageSearch\Dependency\Plugin\ProductPageDataExpanderInterface> $pageDataExpanderPlugins
     * @param \Generated\Shared\Transfer\ProductPageLoadTransfer $productPageLoadTransfer
     * @param bool $isRefresh
     *
     * @return void
     */
    protected function storeData(
        array $productAbstractLocalizedEntities,
        array $productAbstractPageSearchEntities,
        array $pageDataExpanderPlugins,
        ProductPageLoadTransfer $productPageLoadTransfer,
        $isRefresh = false
    ) {
        $pairedEntities = $this->pairProductAbstractLocalizedEntitiesWithProductAbstractPageSearchEntities(
            $productAbstractLocalizedEntities,
            $productAbstractPageSearchEntities,
            $productPageLoadTransfer,
        );

        $productPageSearchTransfers = $this->mapPairedEntitiesToProductPageSearchTransfers(
            $pairedEntities,
            $isRefresh,
        );
        $productPageSearchTransfers = $this->executeProductPageSearchCollectionFilterPlugins($productPageSearchTransfers);
        $indexedProductAbstractPageSearchTransfers = $this->indexProductPageSearchTransfersByLocaleAndIdProductAbstract(
            $productPageSearchTransfers,
        );

        foreach ($pairedEntities as $pairedEntity) {
            /** @var array|null $productAbstractLocalizedEntity */
            $productAbstractLocalizedEntity = $pairedEntity[static::PRODUCT_ABSTRACT_LOCALIZED_ENTITY];
            /** @var \Orm\Zed\ProductPageSearch\Persistence\SpyProductAbstractPageSearch $productAbstractPageSearchEntity */
            $productAbstractPageSearchEntity = $pairedEntity[static::PRODUCT_ABSTRACT_PAGE_SEARCH_ENTITY];
            $store = $pairedEntity[static::STORE_NAME];
            $locale = $pairedEntity[static::LOCALE_NAME];

            if ($productAbstractLocalizedEntity === null || !$this->isActual($productAbstractLocalizedEntity)) {
                $this->deleteProductAbstractPageSearchEntity($productAbstractPageSearchEntity);

                continue;
            }

            $idProductAbstract = $productAbstractLocalizedEntity['fk_product_abstract'];
            $productPageSearchTransfer = $indexedProductAbstractPageSearchTransfers[$locale][$idProductAbstract] ?? null;

            if ($productPageSearchTransfer === null) {
                $this->deleteProductAbstractPageSearchEntity($productAbstractPageSearchEntity);

                continue;
            }

            $this->storeProductAbstractPageSearchEntity(
                $productAbstractLocalizedEntity,
                $productAbstractPageSearchEntity,
                $productPageSearchTransfer,
                $store,
                $locale,
                $pageDataExpanderPlugins,
            );
        }
    }

    /**
     * @param \Orm\Zed\ProductPageSearch\Persistence\SpyProductAbstractPageSearch $productAbstractPageSearchEntity
     *
     * @return void
     */
    protected function deleteProductAbstractPageSearchEntity(SpyProductAbstractPageSearch $productAbstractPageSearchEntity)
    {
        if (!$productAbstractPageSearchEntity->isNew()) {
            $productAbstractPageSearchEntity->delete();
        }
    }

    /**
     * @param array $productAbstractLocalizedEntity
     * @param \Orm\Zed\ProductPageSearch\Persistence\SpyProductAbstractPageSearch $productAbstractPageSearchEntity
     * @param \Generated\Shared\Transfer\ProductPageSearchTransfer $productPageSearchTransfer
     * @param string $storeName
     * @param string $localeName
     * @param array<\Spryker\Zed\ProductPageSearch\Dependency\Plugin\ProductPageDataExpanderInterface> $pageDataExpanderPlugins
     *
     * @return void
     */
    protected function storeProductAbstractPageSearchEntity(
        array $productAbstractLocalizedEntity,
        SpyProductAbstractPageSearch $productAbstractPageSearchEntity,
        ProductPageSearchTransfer $productPageSearchTransfer,
        string $storeName,
        string $localeName,
        array $pageDataExpanderPlugins
    ) {
        $productPageSearchTransfer->setStore($storeName);
        $productPageSearchTransfer->setLocale($localeName);

        $this->expandPageSearchTransferWithPlugins($pageDataExpanderPlugins, $productAbstractLocalizedEntity, $productPageSearchTransfer);

        $searchDocument = $this->productPageSearchMapper->mapToSearchData($productPageSearchTransfer);

        $this->productPageSearchWriter->save($productPageSearchTransfer, $searchDocument, $productAbstractPageSearchEntity);
    }

    /**
     * @param array $productAbstractLocalizedEntity
     *
     * @return bool
     */
    protected function isActual(array $productAbstractLocalizedEntity): bool
    {
        foreach ($productAbstractLocalizedEntity['SpyProductAbstract']['SpyProducts'] as $spyProduct) {
            if ($spyProduct['is_active'] && $this->isSearchable($spyProduct, $productAbstractLocalizedEntity['fk_locale'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $spyProduct
     * @param int $idLocale
     *
     * @return bool
     */
    protected function isSearchable(array $spyProduct, int $idLocale): bool
    {
        foreach ($spyProduct['SpyProductSearches'] as $spyProductSearch) {
            if ($spyProductSearch['fk_locale'] === $idLocale && $spyProductSearch['is_searchable'] === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieves the ProductPageSearchTransfer from the storage entity (if it existed already) or populates it from the localized entity.
     *
     * @param array $productAbstractLocalizedEntity
     * @param \Orm\Zed\ProductPageSearch\Persistence\SpyProductAbstractPageSearch $productAbstractPageSearchEntity
     * @param bool $isRefresh
     *
     * @return \Generated\Shared\Transfer\ProductPageSearchTransfer
     */
    protected function getProductPageSearchTransfer(
        array $productAbstractLocalizedEntity,
        SpyProductAbstractPageSearch $productAbstractPageSearchEntity,
        $isRefresh = false
    ): ProductPageSearchTransfer {
        if ($isRefresh && !$productAbstractPageSearchEntity->isNew()) {
            return $this->refreshProductPageSearchTransfer($productAbstractPageSearchEntity);
        }

        return $this->productPageSearchMapper->mapToProductPageSearchTransfer($productAbstractLocalizedEntity);
    }

    /**
     * @param \Orm\Zed\ProductPageSearch\Persistence\SpyProductAbstractPageSearch $productAbstractPageSearchEntity
     *
     * @return \Generated\Shared\Transfer\ProductPageSearchTransfer
     */
    protected function refreshProductPageSearchTransfer(
        SpyProductAbstractPageSearch $productAbstractPageSearchEntity
    ): ProductPageSearchTransfer {
        return $this->productPageSearchMapper->mapToProductPageSearchTransferFromJson($productAbstractPageSearchEntity->getStructuredData());
    }

    /**
     * @param array<string> $pageDataExpanderPluginNames
     *
     * @return array<\Spryker\Zed\ProductPageSearch\Dependency\Plugin\ProductPageDataExpanderInterface>
     */
    protected function getPageDataExpanderPlugins(array $pageDataExpanderPluginNames)
    {
        if (!$pageDataExpanderPluginNames) {
            return $this->pageDataExpanderPlugins;
        }

        $selectedExpanderPlugins = [];
        foreach ($pageDataExpanderPluginNames as $pageDataExpanderPluginName) {
            $this->assertPageDataExpanderPluginName($pageDataExpanderPluginName);

            $selectedExpanderPlugins[] = $this->pageDataExpanderPlugins[$pageDataExpanderPluginName];
        }

        return $selectedExpanderPlugins;
    }

    /**
     * @param string $pageDataExpanderPluginName
     *
     * @throws \Spryker\Zed\ProductPageSearch\Business\Exception\PluginNotFoundException
     *
     * @return void
     */
    protected function assertPageDataExpanderPluginName($pageDataExpanderPluginName)
    {
        if (!isset($this->pageDataExpanderPlugins[$pageDataExpanderPluginName])) {
            throw new PluginNotFoundException(sprintf('The plugin with this name: %s is not found', $pageDataExpanderPluginName));
        }
    }

    /**
     * @param array<\Spryker\Zed\ProductPageSearch\Dependency\Plugin\ProductPageDataExpanderInterface> $pageDataExpanderPlugins
     * @param array<string, mixed> $productAbstractLocalizedEntity
     * @param \Generated\Shared\Transfer\ProductPageSearchTransfer $productPageSearchTransfer
     *
     * @return void
     */
    protected function expandPageSearchTransferWithPlugins(
        array $pageDataExpanderPlugins,
        array $productAbstractLocalizedEntity,
        ProductPageSearchTransfer $productPageSearchTransfer
    ) {
        foreach ($pageDataExpanderPlugins as $pageDataExpanderPlugin) {
            $pageDataExpanderPlugin->expandProductPageData($productAbstractLocalizedEntity, $productPageSearchTransfer);
        }
    }

    /**
     * - Returns a paired array with all provided entities.
     * - ProductAbstractLocalizedEntities without ProductAbstractPageSearchEntity are paired with a newly created ProductAbstractPageSearchEntity.
     * - ProductAbstractPageSearchEntity without ProductAbstractLocalizedEntities (left outs) are paired with NULL.
     * - ProductAbstractLocalizedEntities are paired multiple times per store.
     *
     * @param array<array<string, mixed>> $productAbstractLocalizedEntities
     * @param array<\Orm\Zed\ProductPageSearch\Persistence\SpyProductAbstractPageSearch> $productAbstractPageSearchEntities
     * @param \Generated\Shared\Transfer\ProductPageLoadTransfer $productPageLoadTransfer
     *
     * @return array
     */
    protected function pairProductAbstractLocalizedEntitiesWithProductAbstractPageSearchEntities(
        array $productAbstractLocalizedEntities,
        array $productAbstractPageSearchEntities,
        ProductPageLoadTransfer $productPageLoadTransfer
    ) {
        $mappedProductAbstractPageSearchEntities = $this->mapProductAbstractPageSearchEntities($productAbstractPageSearchEntities);

        $pairs = [];
        $productPayloadTransfers = $productPageLoadTransfer->getPayloadTransfers();
        foreach ($productAbstractLocalizedEntities as $productAbstractLocalizedEntity) {
            [$pairs, $mappedProductAbstractPageSearchEntities] = $this->pairProductAbstractLocalizedEntityWithProductAbstractPageSearchEntityByStoresAndLocale(
                $productAbstractLocalizedEntity['fk_product_abstract'],
                $productAbstractLocalizedEntity['Locale']['locale_name'],
                $productPayloadTransfers[$productAbstractLocalizedEntity['fk_product_abstract']],
                $productAbstractLocalizedEntity['SpyProductAbstract']['SpyProductAbstractStores'],
                $productAbstractLocalizedEntity,
                $mappedProductAbstractPageSearchEntities,
                $pairs,
            );
        }

        $pairs = $this->pairRemainingProductAbstractPageSearchEntities($mappedProductAbstractPageSearchEntities, $pairs);

        return $pairs;
    }

    /**
     * @param array<int> $productAbstractIds
     *
     * @return array
     */
    protected function findProductAbstractLocalizedEntities(array $productAbstractIds)
    {
        $allProductAbstractLocalizedEntities = [];
        $localesByStore = [];
        foreach ($this->storeFacade->getAllStores() as $storeTransfer) {
            $productAbstractLocalizedEntities = $this
                ->queryContainer
                ->queryProductAbstractLocalizedEntitiesByProductAbstractIdsAndStore($productAbstractIds, $storeTransfer)
                ->find()
                ->getData();

            if (!isset($localesByStore[$storeTransfer->getName()])) {
                $localesByStore[$storeTransfer->getName()] = $storeTransfer->getAvailableLocaleIsoCodes();
            }
            $productConcreteEntities = $this->getProductConcreteEntitiesWithProductSearchEntities($productAbstractIds, $localesByStore[$storeTransfer->getName()]);
            $allProductAbstractLocalizedEntities[] = $this->hydrateProductAbstractLocalizedEntitiesWithProductConcreteEntities($productConcreteEntities, $productAbstractLocalizedEntities);
        }

        return array_merge(...$allProductAbstractLocalizedEntities);
    }

    /**
     * @param array<int> $productAbstractIds
     * @param array<string> $localeIsoCodes
     *
     * @return array
     */
    protected function getProductConcreteEntitiesWithProductSearchEntities(array $productAbstractIds, array $localeIsoCodes): array
    {
        $productConcreteEntities = $this->getProductConcreteEntitiesByProductAbstractIdsAndLocaleIsoCodes($productAbstractIds, $localeIsoCodes);
        $productSearchEntities = $this->getProductSearchEntitiesByProductConcreteIdsAndLocaleIsoCodes(array_column($productConcreteEntities, 'id_product'), $localeIsoCodes);
        $productConcreteEntities = $this->hydrateProductConcreteEntitiesWithProductSearchEntities($productSearchEntities, $productConcreteEntities);

        return $productConcreteEntities;
    }

    /**
     * @param array<int> $productAbstractIds
     * @param array<string> $localeIsoCodes
     *
     * @return array
     */
    protected function getProductConcreteEntitiesByProductAbstractIdsAndLocaleIsoCodes(array $productAbstractIds, array $localeIsoCodes): array
    {
        return $this->queryContainer
            ->queryProductConcretesByAbstractProductIdsAndLocaleIsoCodes($productAbstractIds, $localeIsoCodes)
            ->find()
            ->getData();
    }

    /**
     * @param array<int> $productConcreteIds
     * @param array<string> $localeIsoCodes
     *
     * @return array
     */
    protected function getProductSearchEntitiesByProductConcreteIdsAndLocaleIsoCodes(array $productConcreteIds, array $localeIsoCodes): array
    {
        return $this->queryContainer
            ->queryProductSearchByProductConcreteIdsAndLocaleIsoCodes($productConcreteIds, $localeIsoCodes)
            ->find()
            ->getData();
    }

    /**
     * @param array $productSearchEntities
     * @param array $productConcreteEntities
     *
     * @return array
     */
    protected function hydrateProductConcreteEntitiesWithProductSearchEntities(array $productSearchEntities, array $productConcreteEntities): array
    {
        $productSearchByProductConcreteId = [];

        foreach ($productSearchEntities as $productSearch) {
            $productSearchByProductConcreteId[$productSearch['fk_product']][] = $productSearch;
        }

        foreach ($productConcreteEntities as $key => $productConcreteEntity) {
            $productConcreteId = (int)$productConcreteEntity['id_product'];
            $productConcreteEntities[$key]['SpyProductSearches'] = $productSearchByProductConcreteId[$productConcreteId] ?? [];
        }

        return $productConcreteEntities;
    }

    /**
     * @param array $productCategories
     * @param array $productAbstractLocalizedEntities
     *
     * @return array
     */
    protected function hydrateProductAbstractLocalizedEntitiesWithProductCategories(array $productCategories, array $productAbstractLocalizedEntities)
    {
        $productCategoriesByProductAbstractId = [];

        foreach ($productCategories as $productCategory) {
            $productCategoriesByProductAbstractId[$productCategory['fk_product_abstract']][] = $productCategory;
        }

        foreach ($productAbstractLocalizedEntities as $key => $productAbstractLocalizedEntity) {
            $productAbstractId = (int)$productAbstractLocalizedEntity['fk_product_abstract'];
            $productAbstractLocalizedEntities[$key]['SpyProductAbstract']['SpyProductCategories']
                = $productCategoriesByProductAbstractId[$productAbstractId] ?? [];
        }

        return $productAbstractLocalizedEntities;
    }

    /**
     * @param array $productConcreteData
     * @param array $productAbstractLocalizedEntities
     *
     * @return array
     */
    protected function hydrateProductAbstractLocalizedEntitiesWithProductConcreteEntities(
        array $productConcreteData,
        array $productAbstractLocalizedEntities
    ): array {
        $productConcretesByProductAbstractId = [];
        foreach ($productConcreteData as $productConcrete) {
            $productConcretesByProductAbstractId[$productConcrete['fk_product_abstract']][] = $productConcrete;
        }

        foreach ($productAbstractLocalizedEntities as $key => $productAbstractLocalizedEntity) {
            $productAbstractId = (int)$productAbstractLocalizedEntity['fk_product_abstract'];
            $productAbstractLocalizedEntities[$key]['SpyProductAbstract']['SpyProducts'] = $productConcretesByProductAbstractId[$productAbstractId] ?? [];
        }

        return $productAbstractLocalizedEntities;
    }

    /**
     * @param array<int> $productAbstractIds
     *
     * @return array
     */
    protected function getProductCategoriesByProductAbstractIds(array $productAbstractIds)
    {
        return $this->queryContainer->queryAllProductCategories($productAbstractIds)->find()->getData();
    }

    /**
     * @param array<int> $productAbstractIds
     *
     * @return array<\Orm\Zed\ProductPageSearch\Persistence\SpyProductAbstractPageSearch>
     */
    protected function findProductAbstractPageSearchEntities(array $productAbstractIds)
    {
        return $this->queryContainer->queryProductAbstractSearchPageByIds($productAbstractIds)->find()->getArrayCopy();
    }

    /**
     * @param array<\Orm\Zed\ProductPageSearch\Persistence\SpyProductAbstractPageSearch> $productAbstractPageSearchEntities
     *
     * @return array
     */
    protected function mapProductAbstractPageSearchEntities(array $productAbstractPageSearchEntities)
    {
        $mappedProductAbstractPageSearchEntities = [];
        foreach ($productAbstractPageSearchEntities as $entity) {
            $mappedProductAbstractPageSearchEntities[$entity->getFkProductAbstract()][$entity->getStore()][$entity->getLocale()] = $entity;
        }

        return $mappedProductAbstractPageSearchEntities;
    }

    /**
     * @param array $mappedProductAbstractPageSearchEntities
     * @param array $pairs
     *
     * @return array
     */
    protected function pairRemainingProductAbstractPageSearchEntities(array $mappedProductAbstractPageSearchEntities, array $pairs)
    {
        array_walk_recursive($mappedProductAbstractPageSearchEntities, function (SpyProductAbstractPageSearch $productAbstractPageSearchEntity) use (&$pairs) {
            $pairs[] = [
                static::PRODUCT_ABSTRACT_LOCALIZED_ENTITY => null,
                static::PRODUCT_ABSTRACT_PAGE_SEARCH_ENTITY => $productAbstractPageSearchEntity,
                static::LOCALE_NAME => $productAbstractPageSearchEntity->getLocale(),
                static::STORE_NAME => $productAbstractPageSearchEntity->getStore(),
            ];
        });

        return $pairs;
    }

    /**
     * @param int $idProductAbstract
     * @param string $localeName
     * @param \Generated\Shared\Transfer\ProductPayloadTransfer $productPayloadTransfer
     * @param array $productAbstractStores
     * @param array $productAbstractLocalizedEntity
     * @param array $mappedProductAbstractPageSearchEntities
     * @param array $pairs
     *
     * @return array
     */
    protected function pairProductAbstractLocalizedEntityWithProductAbstractPageSearchEntityByStoresAndLocale(
        $idProductAbstract,
        $localeName,
        ProductPayloadTransfer $productPayloadTransfer,
        array $productAbstractStores,
        array $productAbstractLocalizedEntity,
        array $mappedProductAbstractPageSearchEntities,
        array $pairs
    ) {
        foreach ($productAbstractStores as $productAbstractStore) {
            $storeName = $productAbstractStore['SpyStore']['name'];
            $productAbstractLocalizedEntity[SharedProductPageSearchConfig::PRODUCT_ABSTRACT_PAGE_LOAD_DATA] = $productPayloadTransfer;

            $searchEntity = $mappedProductAbstractPageSearchEntities[$idProductAbstract][$storeName][$localeName] ??
                new SpyProductAbstractPageSearch();

            unset($mappedProductAbstractPageSearchEntities[$idProductAbstract][$storeName][$localeName]);

            $pairs[] = [
                static::PRODUCT_ABSTRACT_LOCALIZED_ENTITY => $productAbstractLocalizedEntity,
                static::PRODUCT_ABSTRACT_PAGE_SEARCH_ENTITY => $searchEntity,
                static::LOCALE_NAME => $localeName,
                static::STORE_NAME => $storeName,
            ];
        }

        return [$pairs, $mappedProductAbstractPageSearchEntities];
    }

    /**
     * @param array $productAbstractLocalizedEntities
     * @param array<int> $productAbstractIds
     *
     * @return array
     */
    protected function hydrateProductAbstractLocalizedEntitiesWithProductAbstractAddToCartSku(
        array $productAbstractLocalizedEntities,
        array $productAbstractIds
    ): array {
        $productConcreteSkuMapByIdProductAbstract = $this->addToCartSkuReader->getProductAbstractAddToCartSkus($productAbstractIds);

        foreach ($productAbstractLocalizedEntities as &$productAbstractLocalizedEntity) {
            $productAbstractId = (int)$productAbstractLocalizedEntity['fk_product_abstract'];
            $productAbstractLocalizedEntity[ProductPageSearchTransfer::ADD_TO_CART_SKU] = $productConcreteSkuMapByIdProductAbstract[$productAbstractId] ?? null;
        }

        return $productAbstractLocalizedEntities;
    }

    /**
     * @param array $pairedEntities
     * @param bool $isRefresh
     *
     * @return array
     */
    protected function mapPairedEntitiesToProductPageSearchTransfers(
        array $pairedEntities,
        bool $isRefresh
    ): array {
        $productAbstractPageSearchTransfers = [];

        foreach ($pairedEntities as $pairedEntity) {
            /** @var array|null $productAbstractLocalizedEntity */
            $productAbstractLocalizedEntity = $pairedEntity[static::PRODUCT_ABSTRACT_LOCALIZED_ENTITY] ?? null;
            /** @var \Orm\Zed\ProductPageSearch\Persistence\SpyProductAbstractPageSearch $productAbstractPageSearchEntity */
            $productAbstractPageSearchEntity = $pairedEntity[static::PRODUCT_ABSTRACT_PAGE_SEARCH_ENTITY];

            if (!$productAbstractLocalizedEntity) {
                continue;
            }

            $productAbstractPageSearchTransfers[] = $this->getProductPageSearchTransfer(
                $productAbstractLocalizedEntity,
                $productAbstractPageSearchEntity,
                $isRefresh,
            );
        }

        return $productAbstractPageSearchTransfers;
    }

    /**
     * @param array<\Generated\Shared\Transfer\ProductPageSearchTransfer> $productPageSearchTransfers
     *
     * @return array<\Generated\Shared\Transfer\ProductPageSearchTransfer>
     */
    protected function executeProductPageSearchCollectionFilterPlugins(array $productPageSearchTransfers): array
    {
        foreach ($this->productPageSearchCollectionFilterPlugins as $productPageSearchCollectionFilterPlugin) {
            $productPageSearchTransfers = $productPageSearchCollectionFilterPlugin->filter($productPageSearchTransfers);
        }

        return $productPageSearchTransfers;
    }

    /**
     * @param array<\Generated\Shared\Transfer\ProductPageSearchTransfer> $productPageSearchTransfers
     *
     * @return array<string, array<int, \Generated\Shared\Transfer\ProductPageSearchTransfer>>
     */
    protected function indexProductPageSearchTransfersByLocaleAndIdProductAbstract(array $productPageSearchTransfers): array
    {
        $indexedProductPageSearchTransfers = [];

        foreach ($productPageSearchTransfers as $productPageSearchTransfer) {
            $idProductAbstract = $productPageSearchTransfer->getIdProductAbstractOrFail();
            $locale = $productPageSearchTransfer->getLocaleOrFail();

            $indexedProductPageSearchTransfers[$locale][$idProductAbstract] = $productPageSearchTransfer;
        }

        return $indexedProductPageSearchTransfers;
    }
}
