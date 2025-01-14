<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOfferMerchantPortalGui\Communication\DataProvider;

use Generated\Shared\Transfer\GuiTableDataRequestTransfer;
use Generated\Shared\Transfer\GuiTableDataResponseTransfer;
use Generated\Shared\Transfer\GuiTableRowDataResponseTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Generated\Shared\Transfer\ProductTableCriteriaTransfer;
use Spryker\Shared\GuiTable\DataProvider\AbstractGuiTableDataProvider;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Zed\ProductOfferMerchantPortalGui\Communication\Builder\ProductNameBuilderInterface;
use Spryker\Zed\ProductOfferMerchantPortalGui\Communication\ConfigurationProvider\ProductGuiTableConfigurationProvider;
use Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Facade\ProductOfferMerchantPortalGuiToLocaleFacadeInterface;
use Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Facade\ProductOfferMerchantPortalGuiToMerchantUserFacadeInterface;
use Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Facade\ProductOfferMerchantPortalGuiToTranslatorFacadeInterface;
use Spryker\Zed\ProductOfferMerchantPortalGui\Persistence\ProductOfferMerchantPortalGuiRepositoryInterface;

class ProductGuiTableDataProvider extends AbstractGuiTableDataProvider
{
    /**
     * @var \Spryker\Zed\ProductOfferMerchantPortalGui\Persistence\ProductOfferMerchantPortalGuiRepositoryInterface
     */
    protected $productOfferMerchantPortalGuiRepository;

    /**
     * @var \Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Facade\ProductOfferMerchantPortalGuiToTranslatorFacadeInterface
     */
    protected $translatorFacade;

    /**
     * @var \Spryker\Zed\ProductOfferMerchantPortalGui\Communication\Builder\ProductNameBuilderInterface
     */
    protected $productNameBuilder;

    /**
     * @var \Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Facade\ProductOfferMerchantPortalGuiToMerchantUserFacadeInterface
     */
    protected $merchantUserFacade;

    /**
     * @var \Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Facade\ProductOfferMerchantPortalGuiToLocaleFacadeInterface
     */
    protected $localeFacade;

    /**
     * @var array<\Spryker\Zed\ProductOfferMerchantPortalGuiExtension\Dependency\Plugin\ProductTableExpanderPluginInterface>
     */
    protected $productTableExpanderPlugins;

    /**
     * @param \Spryker\Zed\ProductOfferMerchantPortalGui\Persistence\ProductOfferMerchantPortalGuiRepositoryInterface $productOfferMerchantPortalGuiRepository
     * @param \Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Facade\ProductOfferMerchantPortalGuiToTranslatorFacadeInterface $translatorFacade
     * @param \Spryker\Zed\ProductOfferMerchantPortalGui\Communication\Builder\ProductNameBuilderInterface $productNameBuilder
     * @param \Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Facade\ProductOfferMerchantPortalGuiToMerchantUserFacadeInterface $merchantUserFacade
     * @param \Spryker\Zed\ProductOfferMerchantPortalGui\Dependency\Facade\ProductOfferMerchantPortalGuiToLocaleFacadeInterface $localeFacade
     * @param array<\Spryker\Zed\ProductOfferMerchantPortalGuiExtension\Dependency\Plugin\ProductTableExpanderPluginInterface> $productTableExpanderPlugins
     */
    public function __construct(
        ProductOfferMerchantPortalGuiRepositoryInterface $productOfferMerchantPortalGuiRepository,
        ProductOfferMerchantPortalGuiToTranslatorFacadeInterface $translatorFacade,
        ProductNameBuilderInterface $productNameBuilder,
        ProductOfferMerchantPortalGuiToMerchantUserFacadeInterface $merchantUserFacade,
        ProductOfferMerchantPortalGuiToLocaleFacadeInterface $localeFacade,
        array $productTableExpanderPlugins
    ) {
        $this->productOfferMerchantPortalGuiRepository = $productOfferMerchantPortalGuiRepository;
        $this->translatorFacade = $translatorFacade;
        $this->productNameBuilder = $productNameBuilder;
        $this->merchantUserFacade = $merchantUserFacade;
        $this->localeFacade = $localeFacade;
        $this->productTableExpanderPlugins = $productTableExpanderPlugins;
    }

    /**
     * @param \Generated\Shared\Transfer\GuiTableDataRequestTransfer $guiTableDataRequestTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    protected function createCriteria(GuiTableDataRequestTransfer $guiTableDataRequestTransfer): AbstractTransfer
    {
        return (new ProductTableCriteriaTransfer())
            ->setLocale($this->localeFacade->getCurrentLocale())
            ->setMerchantReference($this->merchantUserFacade->getCurrentMerchantUser()->getMerchantOrFail()->getMerchantReference());
    }

    /**
     * @param \Generated\Shared\Transfer\ProductTableCriteriaTransfer $criteriaTransfer
     *
     * @return \Generated\Shared\Transfer\GuiTableDataResponseTransfer
     */
    protected function fetchData(AbstractTransfer $criteriaTransfer): GuiTableDataResponseTransfer
    {
        $productConcreteCollectionTransfer = $this->productOfferMerchantPortalGuiRepository->getProductTableData($criteriaTransfer);
        $guiTableDataResponseTransfer = new GuiTableDataResponseTransfer();
        /** @var \Generated\Shared\Transfer\LocaleTransfer $localeTransfer */
        $localeTransfer = $criteriaTransfer->requireLocale()->getLocale();

        foreach ($productConcreteCollectionTransfer->getProducts() as $productConcreteTransfer) {
            $responseData = [
                ProductConcreteTransfer::ID_PRODUCT_CONCRETE => $productConcreteTransfer->getIdProductConcrete(),
                ProductConcreteTransfer::ABSTRACT_SKU => $productConcreteTransfer->getAbstractSku(),
                ProductGuiTableConfigurationProvider::COL_KEY_SKU => $productConcreteTransfer->getSku(),
                ProductGuiTableConfigurationProvider::COL_KEY_NAME => $this->productNameBuilder->buildProductConcreteName($productConcreteTransfer, $localeTransfer),
                ProductGuiTableConfigurationProvider::COL_KEY_STORES => $this->getStoresColumnData($productConcreteTransfer),
                ProductGuiTableConfigurationProvider::COL_KEY_IMAGE => $this->getImageUrl($productConcreteTransfer),
                ProductGuiTableConfigurationProvider::COL_KEY_STATUS => $this->getStatusColumnData($productConcreteTransfer),
                ProductGuiTableConfigurationProvider::COL_KEY_VALID_FROM => $productConcreteTransfer->getValidFrom(),
                ProductGuiTableConfigurationProvider::COL_KEY_VALID_TO => $productConcreteTransfer->getValidTo(),
                ProductGuiTableConfigurationProvider::COL_KEY_OFFERS => $productConcreteTransfer->getNumberOfOffers(),
            ];

            $guiTableDataResponseTransfer->addRow((new GuiTableRowDataResponseTransfer())->setResponseData($responseData));
        }

        /** @var \Generated\Shared\Transfer\PaginationTransfer $paginationTransfer */
        $paginationTransfer = $productConcreteCollectionTransfer->requirePagination()->getPagination();
        $guiTableDataResponseTransfer
            ->setPage($paginationTransfer->requirePage()->getPage())
            ->setPageSize($paginationTransfer->requireMaxPerPage()->getMaxPerPage())
            ->setTotal($paginationTransfer->requireNbResults()->getNbResults());

        $guiTableDataResponseTransfer = $this->executeProductTableExpanderPlugins($guiTableDataResponseTransfer);

        return $guiTableDataResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductConcreteTransfer $productConcreteTransfer
     *
     * @return array<string>
     */
    protected function getStoresColumnData(ProductConcreteTransfer $productConcreteTransfer): array
    {
        $storeTransfers = $productConcreteTransfer->getStores();
        $storeNames = [];

        foreach ($storeTransfers as $storeTransfer) {
            /** @var string $storeName */
            $storeName = $storeTransfer->requireName()->getName();
            $storeNames[] = $storeName;
        }

        return $storeNames;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductConcreteTransfer $productConcreteTransfer
     *
     * @return string
     */
    protected function getStatusColumnData(ProductConcreteTransfer $productConcreteTransfer): string
    {
        $isActiveColumnData = $productConcreteTransfer->getIsActive()
            ? ProductGuiTableConfigurationProvider::COLUMN_DATA_STATUS_ACTIVE
            : ProductGuiTableConfigurationProvider::COLUMN_DATA_STATUS_INACTIVE;

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

        /** @var \Generated\Shared\Transfer\ProductImageSetTransfer $productImageSetTransfer */
        $productImageSetTransfer = $productConcreteTransfer->getImageSets()[0];
        $productImages = $productImageSetTransfer->getProductImages();

        return isset($productImages[0])
            ? $productImages[0]->getExternalUrlSmall()
            : null;
    }

    /**
     * @param \Generated\Shared\Transfer\GuiTableDataResponseTransfer $guiTableDataResponseTransfer
     *
     * @return \Generated\Shared\Transfer\GuiTableDataResponseTransfer
     */
    protected function executeProductTableExpanderPlugins(
        GuiTableDataResponseTransfer $guiTableDataResponseTransfer
    ): GuiTableDataResponseTransfer {
        foreach ($this->productTableExpanderPlugins as $productTableExpanderPlugin) {
            $guiTableDataResponseTransfer = $productTableExpanderPlugin->expandDataResponse($guiTableDataResponseTransfer);
        }

        return $guiTableDataResponseTransfer;
    }
}
