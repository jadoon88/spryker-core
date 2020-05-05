<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOfferMerchantPortalGui\Communication\Table\ProductTable;

use Generated\Shared\Transfer\GuiTableDataTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Generated\Shared\Transfer\ProductCriteriaFilterTransfer;
use Generated\Shared\Transfer\ProductTableCriteriaTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Zed\ProductOfferMerchantPortalGui\Communication\Builder\ProductNameBuilderInterface;
use Spryker\Zed\ProductOfferMerchantPortalGui\Communication\Table\AbstractTableDataProvider;
use Spryker\Zed\ProductOfferMerchantPortalGui\Communication\Table\GuiTableDataRequest\RequestToGuiTableDataRequestHydratorInterface;
use Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Facade\ProductOfferMerchantPortalGuiToLocaleFacadeInterface;
use Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Facade\ProductOfferMerchantPortalGuiToMerchantUserFacadeInterface;
use Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Facade\ProductOfferMerchantPortalGuiToTranslatorFacadeInterface;
use Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Service\ProductOfferMerchantPortalGuiToUtilDateTimeServiceInterface;
use Spryker\Zed\ProductOfferMerchantPortalGui\Persistence\ProductOfferMerchantPortalGuiRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductTableDataProvider extends AbstractTableDataProvider
{
    protected const COLUMN_DATA_STATUS_ACTIVE = 'Active';
    protected const COLUMN_DATA_STATUS_INACTIVE = 'Inactive';

    /**
     * @var \Spryker\Zed\ProductOfferMerchantPortalGui\Persistence\ProductOfferMerchantPortalGuiRepositoryInterface
     */
    protected $productOfferMerchantPortalGuiRepository;

    /**
     * @var \Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Facade\ProductOfferMerchantPortalGuiToTranslatorFacadeInterface
     */
    protected $translatorFacade;

    /**
     * @var \Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Service\ProductOfferMerchantPortalGuiToUtilDateTimeServiceInterface
     */
    protected $utilDateTimeService;

    /**
     * @var \Spryker\Zed\ProductOfferMerchantPortalGui\Communication\Builder\ProductNameBuilderInterface
     */
    protected $productNameBuilder;

    /**
     * @var \Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Facade\ProductOfferMerchantPortalGuiToMerchantUserFacadeInterface
     */
    private $merchantUserFacade;

    /**
     * @var \Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Facade\ProductOfferMerchantPortalGuiToLocaleFacadeInterface
     */
    private $localeFacade;

    /**
     * @var \Spryker\Zed\ProductOfferMerchantPortalGui\Communication\Table\GuiTableDataRequest\RequestToGuiTableDataRequestHydratorInterface
     */
    private $requestToGuiTableDataRequestHydrator;

    /**
     * @param \Spryker\Zed\ProductOfferMerchantPortalGui\Persistence\ProductOfferMerchantPortalGuiRepositoryInterface $productOfferMerchantPortalGuiRepository
     * @param \Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Facade\ProductOfferMerchantPortalGuiToTranslatorFacadeInterface $translatorFacade
     * @param \Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Service\ProductOfferMerchantPortalGuiToUtilDateTimeServiceInterface $utilDateTimeService
     * @param \Spryker\Zed\ProductOfferMerchantPortalGui\Communication\Builder\ProductNameBuilderInterface $productNameBuilder
     * @param \Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Facade\ProductOfferMerchantPortalGuiToMerchantUserFacadeInterface $merchantUserFacade
     * @param \Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Facade\ProductOfferMerchantPortalGuiToLocaleFacadeInterface $localeFacade
     * @param \Spryker\Zed\ProductOfferMerchantPortalGui\Communication\Table\GuiTableDataRequest\RequestToGuiTableDataRequestHydratorInterface $requestToGuiTableDataRequestHydrator
     */
    public function __construct(
        ProductOfferMerchantPortalGuiRepositoryInterface $productOfferMerchantPortalGuiRepository,
        ProductOfferMerchantPortalGuiToTranslatorFacadeInterface $translatorFacade,
        ProductOfferMerchantPortalGuiToUtilDateTimeServiceInterface $utilDateTimeService,
        ProductNameBuilderInterface $productNameBuilder,
        ProductOfferMerchantPortalGuiToMerchantUserFacadeInterface $merchantUserFacade,
        ProductOfferMerchantPortalGuiToLocaleFacadeInterface $localeFacade,
        RequestToGuiTableDataRequestHydratorInterface $requestToGuiTableDataRequestHydrator
    ) {
        $this->productOfferMerchantPortalGuiRepository = $productOfferMerchantPortalGuiRepository;
        $this->translatorFacade = $translatorFacade;
        $this->utilDateTimeService = $utilDateTimeService;
        $this->productNameBuilder = $productNameBuilder;
        $this->merchantUserFacade = $merchantUserFacade;
        $this->localeFacade = $localeFacade;
        $this->requestToGuiTableDataRequestHydrator = $requestToGuiTableDataRequestHydrator;
    }

    /**
     * @return \Spryker\Zed\ProductOfferMerchantPortalGui\Communication\Table\GuiTableDataRequest\RequestToGuiTableDataRequestHydratorInterface
     */
    protected function getRequestToGuiTableDataRequestHydrator(): RequestToGuiTableDataRequestHydratorInterface
    {
        return $this->requestToGuiTableDataRequestHydrator;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    protected function createPersistenceCriteria(Request $request): AbstractTransfer
    {
        $criteria = new ProductTableCriteriaTransfer();
        $criteria->setMerchantUser($this->merchantUserFacade->getCurrentMerchantUser());
        $criteria->setLocale($this->localeFacade->getCurrentLocale());

        return $criteria;
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer|\Generated\Shared\Transfer\ProductTableCriteriaTransfer $persistenceCriteria
     *
     * @return \Generated\Shared\Transfer\GuiTableDataTransfer
     */
    protected function fetchData(AbstractTransfer $persistenceCriteria): GuiTableDataTransfer
    {
        if (!$persistenceCriteria instanceof ProductTableCriteriaTransfer) {
            throw new LogicException(sprintf(
                '%s expects %s as a criteria, %s given.',
                static::class,
                ProductTableCriteriaTransfer::class,
                get_class($persistenceCriteria)
            ));
        }

        $productTableDataTransfer = $this->productOfferMerchantPortalGuiRepository->getProductTableData($persistenceCriteria);
        $productTableDataArray = [];

        foreach ($productTableDataTransfer->getProducts() as $productConcreteTransfer) {
            $productTableDataArray[] = [
                ProductTable::COL_KEY_SKU => $productConcreteTransfer->getSku(),
                ProductTable::COL_KEY_NAME => $this->productNameBuilder->buildProductName($productConcreteTransfer),
                ProductTable::COL_KEY_STORES => $this->getStoresColumnData($productConcreteTransfer),
                ProductTable::COL_KEY_IMAGE => $this->getImageUrl($productConcreteTransfer),
                ProductTable::COL_KEY_STATUS => $this->getStatusColumnData($productConcreteTransfer),
                ProductTable::COL_KEY_VALID_FROM => $this->getFormattedDateTime($productConcreteTransfer->getValidFrom()),
                ProductTable::COL_KEY_VALID_TO => $this->getFormattedDateTime($productConcreteTransfer->getValidTo()),
                ProductTable::COL_KEY_OFFERS => $productConcreteTransfer->getNumberOfOffers(),
            ];
        }

        $paginationTransfer = $productTableDataTransfer->getPagination();

        return (new GuiTableDataTransfer())->setData($productTableDataArray)
            ->setPage($paginationTransfer->getPage())
            ->setPageSize($paginationTransfer->getMaxPerPage())
            ->setTotal($paginationTransfer->getNbResults());
    }

    /**
     * @param \Generated\Shared\Transfer\ProductConcreteTransfer $productConcreteTransfer
     *
     * @return string
     */
    protected function getStoresColumnData(ProductConcreteTransfer $productConcreteTransfer)
    {
        $storeTransfers = $productConcreteTransfer->getStores();
        $storeNames = [];

        foreach ($storeTransfers as $storeTransfer) {
            $storeNames[] = $storeTransfer->getName();
        }

        return implode(', ', $storeNames);
    }

    /**
     * @param \Generated\Shared\Transfer\ProductConcreteTransfer $productConcreteTransfer
     *
     * @return string
     */
    protected function getStatusColumnData(ProductConcreteTransfer $productConcreteTransfer): string
    {
        $isActiveColumnData = $productConcreteTransfer->getIsActive()
            ? static::COLUMN_DATA_STATUS_ACTIVE
            : static::COLUMN_DATA_STATUS_INACTIVE;

        return $this->translatorFacade->trans($isActiveColumnData);
    }

    /**
     * @param \Generated\Shared\Transfer\ProductConcreteTransfer $productConcreteTransfer
     *
     * @return string|null
     */
    protected function getImageUrl(ProductConcreteTransfer $productConcreteTransfer): ?string
    {
        if (!isset($productConcreteTransfer->getImageSets()[0])) {
            return null;
        }

        $productImageSetTransfer = $productConcreteTransfer->getImageSets()[0];

        return isset($productImageSetTransfer->getProductImages()[0])
            ? $productImageSetTransfer->getProductImages()[0]->getExternalUrlSmall()
            : null;
    }

    /**
     * @param string|null $dateTime
     *
     * @return string|null
     */
    protected function getFormattedDateTime(?string $dateTime): ?string
    {
        return $dateTime ? $this->utilDateTimeService->formatDateTimeToIso($dateTime) : null;
    }
}
