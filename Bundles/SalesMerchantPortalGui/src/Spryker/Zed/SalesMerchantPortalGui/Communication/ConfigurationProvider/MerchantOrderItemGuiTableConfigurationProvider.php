<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesMerchantPortalGui\Communication\ConfigurationProvider;

use Generated\Shared\Transfer\GuiTableConfigurationTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantOrderItemTransfer;
use Generated\Shared\Transfer\MerchantOrderTransfer;
use Spryker\Shared\GuiTable\Configuration\Builder\GuiTableConfigurationBuilderInterface;
use Spryker\Shared\GuiTable\GuiTableFactoryInterface;
use Spryker\Zed\SalesMerchantPortalGui\Dependency\Facade\SalesMerchantPortalGuiToMerchantOmsFacadeInterface;
use Spryker\Zed\SalesMerchantPortalGui\Dependency\Facade\SalesMerchantPortalGuiToMerchantUserFacadeInterface;
use Spryker\Zed\SalesMerchantPortalGui\Dependency\Facade\SalesMerchantPortalGuiToTranslatorFacadeInterface;

class MerchantOrderItemGuiTableConfigurationProvider implements MerchantOrderItemGuiTableConfigurationProviderInterface
{
    /**
     * @var string
     */
    public const COL_KEY_SKU = 'sku';

    /**
     * @var string
     */
    public const COL_KEY_IMAGE = 'image';

    /**
     * @var string
     */
    public const COL_KEY_NAME = 'name';

    /**
     * @var string
     */
    public const COL_KEY_QUANTITY = 'quantity';

    /**
     * @var string
     */
    public const COL_KEY_STATE = 'state';

    /**
     * @var string
     */
    public const COL_KEY_ACTION_IDS = 'actionIds';

    /**
     * @var string
     */
    protected const MESSAGE_NO_BATCH_ACTION_AVAILABLE_FOR_ITEMS = 'There are no applicable actions for the selected items.';

    /**
     * @uses \Spryker\Zed\SalesMerchantPortalGui\Communication\Controller\ItemListController::tableDataAction()
     *
     * @var string
     */
    protected const DATA_URL = '/sales-merchant-portal-gui/item-list/table-data';

    /**
     * @var \Spryker\Zed\SalesMerchantPortalGui\Dependency\Facade\SalesMerchantPortalGuiToMerchantOmsFacadeInterface
     */
    protected $merchantOmsFacade;

    /**
     * @var \Spryker\Zed\SalesMerchantPortalGui\Dependency\Facade\SalesMerchantPortalGuiToMerchantUserFacadeInterface
     */
    protected $merchantUserFacade;

    /**
     * @var \Spryker\Shared\GuiTable\GuiTableFactoryInterface
     */
    protected $guiTableFactory;

    /**
     * @var array<\Spryker\Zed\SalesMerchantPortalGuiExtension\Dependency\Plugin\MerchantOrderItemTableExpanderPluginInterface>
     */
    protected $merchantOrderItemTableExpanderPlugins;

    /**
     * @var \Spryker\Zed\SalesMerchantPortalGui\Dependency\Facade\SalesMerchantPortalGuiToTranslatorFacadeInterface
     */
    protected $translatorFacade;

    /**
     * @param \Spryker\Zed\SalesMerchantPortalGui\Dependency\Facade\SalesMerchantPortalGuiToMerchantOmsFacadeInterface $merchantOmsFacade
     * @param \Spryker\Zed\SalesMerchantPortalGui\Dependency\Facade\SalesMerchantPortalGuiToMerchantUserFacadeInterface $merchantUserFacade
     * @param \Spryker\Shared\GuiTable\GuiTableFactoryInterface $guiTableFactory
     * @param \Spryker\Zed\SalesMerchantPortalGui\Dependency\Facade\SalesMerchantPortalGuiToTranslatorFacadeInterface $translatorFacade
     * @param array<\Spryker\Zed\SalesMerchantPortalGuiExtension\Dependency\Plugin\MerchantOrderItemTableExpanderPluginInterface> $merchantOrderItemTableExpanderPlugins
     */
    public function __construct(
        SalesMerchantPortalGuiToMerchantOmsFacadeInterface $merchantOmsFacade,
        SalesMerchantPortalGuiToMerchantUserFacadeInterface $merchantUserFacade,
        GuiTableFactoryInterface $guiTableFactory,
        SalesMerchantPortalGuiToTranslatorFacadeInterface $translatorFacade,
        array $merchantOrderItemTableExpanderPlugins = []
    ) {
        $this->merchantOmsFacade = $merchantOmsFacade;
        $this->merchantUserFacade = $merchantUserFacade;
        $this->guiTableFactory = $guiTableFactory;
        $this->merchantOrderItemTableExpanderPlugins = $merchantOrderItemTableExpanderPlugins;
        $this->translatorFacade = $translatorFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantOrderTransfer $merchantOrderTransfer
     *
     * @return \Generated\Shared\Transfer\GuiTableConfigurationTransfer
     */
    public function getConfiguration(MerchantOrderTransfer $merchantOrderTransfer): GuiTableConfigurationTransfer
    {
        $guiTableConfigurationBuilder = $this->guiTableFactory->createConfigurationBuilder();

        $guiTableConfigurationBuilder = $this->addColumns($guiTableConfigurationBuilder);
        $guiTableConfigurationBuilder = $this->addFilters($guiTableConfigurationBuilder);
        $guiTableConfigurationBuilder = $this->addRowActions($guiTableConfigurationBuilder, $merchantOrderTransfer);
        $guiTableConfigurationBuilder = $this->addBatchActions($guiTableConfigurationBuilder, $merchantOrderTransfer);

        $guiTableConfigurationBuilder
            ->setDataSourceUrl(static::DATA_URL)
            ->setDefaultPageSize(10)
            ->setIsItemSelectionEnabled(true);

        $guiTableConfigurationTransfer = $guiTableConfigurationBuilder->createConfiguration();

        foreach ($this->merchantOrderItemTableExpanderPlugins as $merchantOrderItemTableExpanderPlugin) {
            $guiTableConfigurationTransfer = $merchantOrderItemTableExpanderPlugin->expandConfiguration($guiTableConfigurationTransfer);
        }

        return $guiTableConfigurationTransfer;
    }

    /**
     * @param \Spryker\Shared\GuiTable\Configuration\Builder\GuiTableConfigurationBuilderInterface $guiTableConfigurationBuilder
     *
     * @return \Spryker\Shared\GuiTable\Configuration\Builder\GuiTableConfigurationBuilderInterface
     */
    protected function addColumns(GuiTableConfigurationBuilderInterface $guiTableConfigurationBuilder): GuiTableConfigurationBuilderInterface
    {
        $guiTableConfigurationBuilder->addColumnText(static::COL_KEY_SKU, 'SKU', true, false)
            ->addColumnImage(static::COL_KEY_IMAGE, 'Image', false, true, static::COL_KEY_NAME)
            ->addColumnText(static::COL_KEY_NAME, 'Name', true, true)
            ->addColumnText(static::COL_KEY_QUANTITY, 'Quantity', false, true)
            ->addColumnChip(static::COL_KEY_STATE, 'State', true, true, 'green');

        return $guiTableConfigurationBuilder;
    }

    /**
     * @param \Spryker\Shared\GuiTable\Configuration\Builder\GuiTableConfigurationBuilderInterface $guiTableConfigurationBuilder
     *
     * @return \Spryker\Shared\GuiTable\Configuration\Builder\GuiTableConfigurationBuilderInterface
     */
    protected function addFilters(GuiTableConfigurationBuilderInterface $guiTableConfigurationBuilder): GuiTableConfigurationBuilderInterface
    {
        $guiTableConfigurationBuilder->addFilterSelect(
            'orderItemStates',
            'State',
            true,
            $this->getStatesOptions(),
        );

        return $guiTableConfigurationBuilder;
    }

    /**
     * @return array<string>
     */
    protected function getStatesOptions(): array
    {
        $idMerchant = $this->merchantUserFacade->getCurrentMerchantUser()->getIdMerchant();
        $merchantCriteriaTransfer = (new MerchantCriteriaTransfer())->setIdMerchant($idMerchant);
        $stateMachineProcessTransfer = $this->merchantOmsFacade->getMerchantOmsProcessByMerchant($merchantCriteriaTransfer);

        return array_map(function (string $stateName) {
            return mb_convert_case($stateName, MB_CASE_TITLE);
        }, $stateMachineProcessTransfer->getStateNames());
    }

    /**
     * @param \Spryker\Shared\GuiTable\Configuration\Builder\GuiTableConfigurationBuilderInterface $guiTableConfigurationBuilder
     * @param \Generated\Shared\Transfer\MerchantOrderTransfer $merchantOrderTransfer
     *
     * @return \Spryker\Shared\GuiTable\Configuration\Builder\GuiTableConfigurationBuilderInterface
     */
    protected function addRowActions(
        GuiTableConfigurationBuilderInterface $guiTableConfigurationBuilder,
        MerchantOrderTransfer $merchantOrderTransfer
    ): GuiTableConfigurationBuilderInterface {
        foreach ($merchantOrderTransfer->getManualEvents() as $manualEvent) {
            $guiTableConfigurationBuilder->addRowActionHttp(
                $manualEvent,
                $manualEvent,
                sprintf(
                    '/sales-merchant-portal-gui/trigger-merchant-oms/batch/?merchant-order-id=%d&event-name=%s&merchant-order-ids=[${rowId}]',
                    $merchantOrderTransfer->getIdMerchantOrder(),
                    $manualEvent,
                ),
            );
        }

        $guiTableConfigurationBuilder->setRowActionRowIdPath(MerchantOrderItemTransfer::ID_MERCHANT_ORDER_ITEM)
            ->setAvailableRowActionsPath(static::COL_KEY_ACTION_IDS);

        return $guiTableConfigurationBuilder;
    }

    /**
     * @param \Spryker\Shared\GuiTable\Configuration\Builder\GuiTableConfigurationBuilderInterface $guiTableConfigurationBuilder
     * @param \Generated\Shared\Transfer\MerchantOrderTransfer $merchantOrderTransfer
     *
     * @return \Spryker\Shared\GuiTable\Configuration\Builder\GuiTableConfigurationBuilderInterface
     */
    protected function addBatchActions(
        GuiTableConfigurationBuilderInterface $guiTableConfigurationBuilder,
        MerchantOrderTransfer $merchantOrderTransfer
    ): GuiTableConfigurationBuilderInterface {
        foreach ($merchantOrderTransfer->getManualEvents() as $manualEvent) {
            $guiTableConfigurationBuilder->addBatchActionHttp(
                $manualEvent,
                $manualEvent,
                sprintf(
                    '/sales-merchant-portal-gui/trigger-merchant-oms/batch/?merchant-order-id=%d&event-name=%s&merchant-order-ids=${rowIds}',
                    $merchantOrderTransfer->getIdMerchantOrder(),
                    $manualEvent,
                ),
            );
        }

        $guiTableConfigurationBuilder->setBatchActionRowIdPath(MerchantOrderItemTransfer::ID_MERCHANT_ORDER_ITEM)
            ->setAvailableBatchActionsPath(static::COL_KEY_ACTION_IDS)
            ->setNoBatchActionsMessage(
                $this->translatorFacade->trans(static::MESSAGE_NO_BATCH_ACTION_AVAILABLE_FOR_ITEMS),
            );

        return $guiTableConfigurationBuilder;
    }
}
