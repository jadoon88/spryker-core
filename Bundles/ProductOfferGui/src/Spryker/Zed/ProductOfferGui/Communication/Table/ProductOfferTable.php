<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOfferGui\Communication\Table;

use Generated\Shared\Transfer\QueryCriteriaTransfer;
use Orm\Zed\Product\Persistence\Map\SpyProductLocalizedAttributesTableMap;
use Orm\Zed\Product\Persistence\Map\SpyProductTableMap;
use Orm\Zed\ProductOffer\Persistence\Map\SpyProductOfferStoreTableMap;
use Orm\Zed\ProductOffer\Persistence\Map\SpyProductOfferTableMap;
use Orm\Zed\ProductOffer\Persistence\SpyProductOfferQuery;
use Orm\Zed\Store\Persistence\Map\SpyStoreTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Shared\ProductOfferGui\ProductOfferGuiConfig as SharedProductOfferGuiConfig;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;
use Spryker\Zed\ProductOfferGui\Communication\Form\ApprovalStatusForm;
use Spryker\Zed\ProductOfferGui\Dependency\Facade\ProductOfferGuiToLocaleFacadeInterface;
use Spryker\Zed\ProductOfferGui\Dependency\Facade\ProductOfferGuiToProductOfferFacadeInterface;
use Spryker\Zed\ProductOfferGui\Persistence\ProductOfferGuiRepositoryInterface;
use Spryker\Zed\ProductOfferGui\ProductOfferGuiConfig;

class ProductOfferTable extends AbstractTable
{
    /**
     * @var string
     */
    protected const COL_STORES = 'stores';

    /**
     * @var string
     */
    protected const COL_ACTIONS = 'actions';

    /**
     * @var string
     */
    protected const COL_PRODUCT_NAME = 'product_name';

    /**
     * @var string
     */
    protected const COL_ID_PRODUCT_CONCRETE = 'id_product_concrete';

    /**
     * @var string
     */
    protected const STORE_CLASS_LABEL = 'label-info';

    /**
     * @var array<string, string>
     */
    protected const APPROVAL_STATUS_CLASS_LABEL_MAPPING = [
        SharedProductOfferGuiConfig::STATUS_WAITING_FOR_APPROVAL => 'label-warning',
        SharedProductOfferGuiConfig::STATUS_APPROVED => 'label-info',
        SharedProductOfferGuiConfig::STATUS_DENIED => 'label-danger',
    ];

    /**
     * @var array<string, string>
     */
    protected const APPROVAL_STATUS_CLASS_BUTTON_MAPPING = [
        SharedProductOfferGuiConfig::STATUS_APPROVED => 'btn-create',
        SharedProductOfferGuiConfig::STATUS_DENIED => 'btn-remove',
    ];

    /**
     * @var \Orm\Zed\ProductOffer\Persistence\SpyProductOfferQuery<mixed>
     */
    protected $productOfferQuery;

    /**
     * @var \Spryker\Zed\ProductOfferGui\Dependency\Facade\ProductOfferGuiToLocaleFacadeInterface
     */
    protected $localeFacade;

    /**
     * @var \Spryker\Zed\ProductOfferGui\Dependency\Facade\ProductOfferGuiToProductOfferFacadeInterface
     */
    protected $productOfferFacade;

    /**
     * @var \Spryker\Zed\ProductOfferGui\Persistence\ProductOfferGuiRepositoryInterface
     */
    protected $repository;

    /**
     * @var array<\Spryker\Zed\ProductOfferGuiExtension\Dependency\Plugin\ProductOfferTableExpanderPluginInterface>
     */
    protected $productOfferTableExpanderPlugins;

    /**
     * @param \Orm\Zed\ProductOffer\Persistence\SpyProductOfferQuery<mixed> $productOfferQuery
     * @param \Spryker\Zed\ProductOfferGui\Dependency\Facade\ProductOfferGuiToLocaleFacadeInterface $localeFacade
     * @param \Spryker\Zed\ProductOfferGui\Dependency\Facade\ProductOfferGuiToProductOfferFacadeInterface $productOfferFacade
     * @param \Spryker\Zed\ProductOfferGui\Persistence\ProductOfferGuiRepositoryInterface $repository
     * @param array<\Spryker\Zed\ProductOfferGuiExtension\Dependency\Plugin\ProductOfferTableExpanderPluginInterface> $productOfferTableExpanderPlugins
     */
    public function __construct(
        SpyProductOfferQuery $productOfferQuery,
        ProductOfferGuiToLocaleFacadeInterface $localeFacade,
        ProductOfferGuiToProductOfferFacadeInterface $productOfferFacade,
        ProductOfferGuiRepositoryInterface $repository,
        array $productOfferTableExpanderPlugins
    ) {
        $this->productOfferQuery = $productOfferQuery;
        $this->localeFacade = $localeFacade;
        $this->productOfferFacade = $productOfferFacade;
        $this->repository = $repository;
        $this->productOfferTableExpanderPlugins = $productOfferTableExpanderPlugins;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config): TableConfiguration
    {
        $url = Url::generate(
            '/table',
            $this->getRequest()->query->all(),
        );
        $config->setUrl($url);

        $config = $this->setHeader($config);

        $config->setSortable([
            SpyProductOfferTableMap::COL_ID_PRODUCT_OFFER,
            SpyProductOfferTableMap::COL_PRODUCT_OFFER_REFERENCE,
            SpyProductOfferTableMap::COL_CONCRETE_SKU,
            static::COL_PRODUCT_NAME,
            SpyProductOfferTableMap::COL_APPROVAL_STATUS,
            SpyProductOfferTableMap::COL_IS_ACTIVE,
        ]);

        $config->setRawColumns([
            static::COL_ACTIONS,
            SpyProductOfferTableMap::COL_APPROVAL_STATUS,
            SpyProductOfferTableMap::COL_IS_ACTIVE,
            SpyProductOfferStoreTableMap::COL_FK_STORE,
        ]);
        $config->setDefaultSortField(SpyProductOfferTableMap::COL_ID_PRODUCT_OFFER, TableConfiguration::SORT_DESC);

        $config->setSearchable([
            SpyProductOfferTableMap::COL_ID_PRODUCT_OFFER,
            SpyProductOfferTableMap::COL_PRODUCT_OFFER_REFERENCE,
            SpyProductOfferTableMap::COL_CONCRETE_SKU,
            SpyProductLocalizedAttributesTableMap::COL_NAME,
            SpyProductOfferTableMap::COL_APPROVAL_STATUS,
            SpyProductOfferTableMap::COL_IS_ACTIVE,
        ]);

        foreach ($this->productOfferTableExpanderPlugins as $productOfferTableExpanderPlugin) {
            $config = $productOfferTableExpanderPlugin->expandTableConfiguration($config);
        }

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function setHeader(TableConfiguration $config): TableConfiguration
    {
        $baseData = [
            SpyProductOfferTableMap::COL_ID_PRODUCT_OFFER => 'Offer ID',
            SpyProductOfferTableMap::COL_PRODUCT_OFFER_REFERENCE => 'Reference',
            SpyProductOfferTableMap::COL_CONCRETE_SKU => 'SKU',
            static::COL_PRODUCT_NAME => 'Name',
            SpyProductOfferTableMap::COL_APPROVAL_STATUS => 'Approval',
            SpyProductOfferTableMap::COL_IS_ACTIVE => 'Status',
            SpyProductOfferStoreTableMap::COL_FK_STORE => 'Stores',
            static::COL_ACTIONS => 'Actions',
        ];

        $config->setHeader($baseData);

        return $config;
    }

    /**
     * @return \Orm\Zed\ProductOffer\Persistence\SpyProductOfferQuery<mixed>
     */
    protected function prepareQuery(): SpyProductOfferQuery
    {
        $this->productOfferQuery = $this->repository->mapQueryCriteriaTransferToModelCriteria(
            $this->productOfferQuery,
            $this->buildQueryCriteriaTransfer(),
        );

        $this->total = $this->productOfferQuery->count();

        /** @var literal-string $where */
        $where = sprintf(
            '%s = (%s)',
            SpyProductLocalizedAttributesTableMap::COL_FK_LOCALE,
            $this->localeFacade->getCurrentLocale()->getIdLocale(),
        );
        $this->productOfferQuery
            ->groupByIdProductOffer()
            ->useSpyProductOfferStoreQuery(null, Criteria::LEFT_JOIN)
                ->leftJoinSpyStore()
                ->withColumn(
                    sprintf('GROUP_CONCAT(%s)', SpyStoreTableMap::COL_NAME),
                    static::COL_STORES,
                )
            ->endUse()
            ->addJoin(SpyProductOfferTableMap::COL_CONCRETE_SKU, SpyProductTableMap::COL_SKU, Criteria::INNER_JOIN)
            ->addJoin(SpyProductTableMap::COL_ID_PRODUCT, SpyProductLocalizedAttributesTableMap::COL_FK_PRODUCT, Criteria::INNER_JOIN)
            ->where($where)
            ->withColumn(SpyProductLocalizedAttributesTableMap::COL_NAME, static::COL_PRODUCT_NAME)
            ->withColumn(
                SpyProductLocalizedAttributesTableMap::COL_FK_PRODUCT,
                static::COL_ID_PRODUCT_CONCRETE,
            );

        return $this->productOfferQuery;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array<mixed>
     */
    protected function prepareData(TableConfiguration $config): array
    {
        $queryResults = $this->runQuery($this->prepareQuery(), $config);
        $results = [];

        foreach ($queryResults as $item) {
            $rowData = [
                SpyProductOfferTableMap::COL_ID_PRODUCT_OFFER => $this->formatInt(
                    $item[SpyProductOfferTableMap::COL_ID_PRODUCT_OFFER],
                ),
                SpyProductOfferTableMap::COL_PRODUCT_OFFER_REFERENCE => $item[SpyProductOfferTableMap::COL_PRODUCT_OFFER_REFERENCE],
                SpyProductOfferTableMap::COL_CONCRETE_SKU => $item[SpyProductOfferTableMap::COL_CONCRETE_SKU],
                static::COL_PRODUCT_NAME => $item[static::COL_PRODUCT_NAME],
                SpyProductOfferTableMap::COL_APPROVAL_STATUS => $this->createStatusLabel($item),
                SpyProductOfferTableMap::COL_IS_ACTIVE => $this->getActiveLabel($item[SpyProductOfferTableMap::COL_IS_ACTIVE]),
                SpyProductOfferStoreTableMap::COL_FK_STORE => $this->createStoresLabel($item),
                static::COL_ACTIONS => $this->buildLinks($item),
            ];

            foreach ($this->productOfferTableExpanderPlugins as $productOfferTableExpanderPlugin) {
                $rowData = $productOfferTableExpanderPlugin->expandData($rowData, $item);
            }

            $results[] = $rowData;
        }

        return $results;
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return string
     */
    protected function buildLinks(array $item): string
    {
        $buttons = [];
        $buttons[] = $this->generateViewButton(
            Url::generate(
                ProductOfferGuiConfig::URL_VIEW,
                [
                    ProductOfferGuiConfig::REQUEST_PARAM_ID_PRODUCT_OFFER => $item[SpyProductOfferTableMap::COL_ID_PRODUCT_OFFER],
                ],
            ),
            'View',
            ['icon' => 'fa fa fa-eye', 'class' => 'btn-info'],
        );

        $availableApprovalStatusButtonsMapping = static::APPROVAL_STATUS_CLASS_BUTTON_MAPPING;
        unset($availableApprovalStatusButtonsMapping[$item[SpyProductOfferTableMap::COL_APPROVAL_STATUS]]);

        $availableApprovalStatuses = $this->productOfferFacade
            ->getApplicableApprovalStatuses($item[SpyProductOfferTableMap::COL_APPROVAL_STATUS]);

        foreach ($availableApprovalStatuses as $availableApprovalStatus) {
            $iconClass = $availableApprovalStatus === 'approved' ? 'fa fa fa-caret-right' : 'fa fa fa-trash';

            $buttons[] = $this->generateFormButton(
                Url::generate(
                    ProductOfferGuiConfig::URL_UPDATE_APPROVAL_STATUS,
                    [
                        ProductOfferGuiConfig::REQUEST_PARAM_ID_PRODUCT_OFFER => $item[SpyProductOfferTableMap::COL_ID_PRODUCT_OFFER],
                        ProductOfferGuiConfig::REQUEST_PARAM_APPROVAL_STATUS => $availableApprovalStatus,
                        ProductOfferGuiConfig::REQUEST_PARAM_ID_PRODUCT_CONCRETE => $item[static::COL_ID_PRODUCT_CONCRETE],
                    ],
                ),
                $availableApprovalStatus . '_offer_button',
                ApprovalStatusForm::class,
                ['icon' => $iconClass, 'class' => $availableApprovalStatusButtonsMapping[$availableApprovalStatus]],
            );
        }

        return implode(' ', $buttons);
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return string
     */
    protected function createStatusLabel(array $item): string
    {
        $currentStatus = $item[SpyProductOfferTableMap::COL_APPROVAL_STATUS];

        if (!isset(static::APPROVAL_STATUS_CLASS_LABEL_MAPPING[$currentStatus])) {
            return $currentStatus;
        }

        return $this->generateLabel($currentStatus, static::APPROVAL_STATUS_CLASS_LABEL_MAPPING[$currentStatus]);
    }

    /**
     * @param bool $isActive
     *
     * @return string
     */
    public function getActiveLabel(bool $isActive): string
    {
        return $isActive ? $this->generateLabel('Active', static::STORE_CLASS_LABEL) : $this->generateLabel('Inactive', 'label-danger');
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return string
     */
    protected function createStoresLabel(array $item): string
    {
        $storeNames = explode(',', $item[static::COL_STORES]);

        $storeLabels = [];
        foreach ($storeNames as $storeName) {
            if (!$storeName) {
                continue;
            }

            $storeLabels[] = $this->generateLabel($storeName, static::STORE_CLASS_LABEL);
        }

        return implode(' ', $storeLabels);
    }

    /**
     * @return \Generated\Shared\Transfer\QueryCriteriaTransfer
     */
    protected function buildQueryCriteriaTransfer(): QueryCriteriaTransfer
    {
        $queryCriteriaTransfer = new QueryCriteriaTransfer();

        foreach ($this->productOfferTableExpanderPlugins as $productOfferTableExpanderPlugin) {
            $queryCriteriaTransfer = $productOfferTableExpanderPlugin->expandQueryCriteria($queryCriteriaTransfer);
        }

        return $queryCriteriaTransfer;
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria<mixed> $query
     *
     * @return int
     */
    protected function countTotal(ModelCriteria $query): int
    {
        return $this->total;
    }
}
