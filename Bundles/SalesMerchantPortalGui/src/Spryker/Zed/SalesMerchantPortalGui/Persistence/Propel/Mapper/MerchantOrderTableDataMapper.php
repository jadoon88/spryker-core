<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesMerchantPortalGui\Persistence\Propel\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\MerchantOrderCollectionTransfer;
use Generated\Shared\Transfer\MerchantOrderTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Orm\Zed\MerchantSalesOrder\Persistence\Map\SpyMerchantSalesOrderTableMap;
use Orm\Zed\MerchantSalesOrder\Persistence\Map\SpyMerchantSalesOrderTotalsTableMap;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderTableMap;

class MerchantOrderTableDataMapper
{
    /**
     * @uses \Spryker\Zed\SalesMerchantPortalGui\Communication\ConfigurationProvider\MerchantOrderGuiTableConfigurationProvider::COL_KEY_REFERENCE
     *
     * @var string
     */
    protected const COL_KEY_REFERENCE = 'reference';

    /**
     * @uses \Spryker\Zed\SalesMerchantPortalGui\Communication\ConfigurationProvider\MerchantOrderGuiTableConfigurationProvider::COL_KEY_MERCHANT_REFERENCE
     *
     * @var string
     */
    protected const COL_KEY_MERCHANT_REFERENCE = 'merchantReference';

    /**
     * @uses \Spryker\Zed\SalesMerchantPortalGui\Communication\ConfigurationProvider\MerchantOrderGuiTableConfigurationProvider::COL_KEY_CREATED
     *
     * @var string
     */
    protected const COL_KEY_CREATED = 'created';

    /**
     * @uses \Spryker\Zed\SalesMerchantPortalGui\Communication\ConfigurationProvider\MerchantOrderGuiTableConfigurationProvider::COL_KEY_CUSTOMER
     *
     * @var string
     */
    protected const COL_KEY_CUSTOMER = 'customer';

    /**
     * @uses \Spryker\Zed\SalesMerchantPortalGui\Communication\ConfigurationProvider\MerchantOrderGuiTableConfigurationProvider::COL_KEY_EMAIL
     *
     * @var string
     */
    protected const COL_KEY_EMAIL = 'Email';

    /**
     * @uses \Spryker\Zed\SalesMerchantPortalGui\Communication\ConfigurationProvider\MerchantOrderGuiTableConfigurationProvider::COL_KEY_ITEMS_STATES
     *
     * @var string
     */
    protected const COL_KEY_ITEMS_STATES = 'itemsStates';

    /**
     * @uses \Spryker\Zed\SalesMerchantPortalGui\Communication\ConfigurationProvider\MerchantOrderGuiTableConfigurationProvider::COL_KEY_GRAND_TOTAL
     *
     * @var string
     */
    protected const COL_KEY_GRAND_TOTAL = 'grandTotal';

    /**
     * @uses \Spryker\Zed\SalesMerchantPortalGui\Communication\ConfigurationProvider\MerchantOrderGuiTableConfigurationProvider::COL_KEY_NUMBER_OF_ITEMS
     *
     * @var string
     */
    protected const COL_KEY_NUMBER_OF_ITEMS = 'numberOfItems';

    /**
     * @uses \Spryker\Zed\SalesMerchantPortalGui\Communication\ConfigurationProvider\MerchantOrderGuiTableConfigurationProvider::COL_KEY_STORE
     *
     * @var string
     */
    public const COL_KEY_STORE = 'store';

    /**
     * @var array<string, string>
     */
    public const MERCHANT_ORDER_DATA_COLUMN_MAP = [
        self::COL_KEY_REFERENCE => SpySalesOrderTableMap::COL_ORDER_REFERENCE,
        self::COL_KEY_MERCHANT_REFERENCE => SpyMerchantSalesOrderTableMap::COL_MERCHANT_SALES_ORDER_REFERENCE,
        self::COL_KEY_CREATED => SpyMerchantSalesOrderTableMap::COL_CREATED_AT,
        self::COL_KEY_CUSTOMER => SpySalesOrderTableMap::COL_LAST_NAME,
        self::COL_KEY_EMAIL => SpySalesOrderTableMap::COL_EMAIL,
        self::COL_KEY_ITEMS_STATES => MerchantOrderTransfer::ITEM_STATES,
        self::COL_KEY_GRAND_TOTAL => SpyMerchantSalesOrderTotalsTableMap::COL_GRAND_TOTAL,
        self::COL_KEY_NUMBER_OF_ITEMS => MerchantOrderTransfer::MERCHANT_ORDER_ITEM_COUNT,
        self::COL_KEY_STORE => SpySalesOrderTableMap::COL_STORE,
    ];

    /**
     * @param array $merchantOrderTableDataArray
     * @param \Generated\Shared\Transfer\MerchantOrderCollectionTransfer $merchantOrderCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantOrderCollectionTransfer
     */
    public function mapMerchantOrderTableDataArrayToMerchantOrderCollectionTransfer(
        array $merchantOrderTableDataArray,
        MerchantOrderCollectionTransfer $merchantOrderCollectionTransfer
    ): MerchantOrderCollectionTransfer {
        $merchantOrderTransfers = [];

        foreach ($merchantOrderTableDataArray as $merchantOrderTableRowDataArray) {
            $merchantOrderTableRowDataArray = $this->prepareItemsStatesTableData($merchantOrderTableRowDataArray);

            $merchantOrderTransfer = (new MerchantOrderTransfer())->fromArray($merchantOrderTableRowDataArray, true);
            $orderTransfer = (new OrderTransfer())
                ->fromArray($merchantOrderTableRowDataArray, true)
                ->setSalutation(
                    SpySalesOrderTableMap::getValueSet(SpySalesOrderTableMap::COL_SALUTATION)[$merchantOrderTableRowDataArray[OrderTransfer::SALUTATION]],
                );
            $totalsTransfer = (new TotalsTransfer())->setGrandTotal($merchantOrderTableRowDataArray[TotalsTransfer::GRAND_TOTAL]);

            $merchantOrderTransfer->setOrder($orderTransfer);
            $merchantOrderTransfer->setTotals($totalsTransfer);

            $merchantOrderTransfers[] = $merchantOrderTransfer;
        }

        $merchantOrderCollectionTransfer->setMerchantOrders(new ArrayObject($merchantOrderTransfers));

        return $merchantOrderCollectionTransfer;
    }

    /**
     * @param array $merchantOrderTableRowDataArray
     *
     * @return array
     */
    protected function prepareItemsStatesTableData(array $merchantOrderTableRowDataArray): array
    {
        $itemsStates = explode(',', $merchantOrderTableRowDataArray[MerchantOrderTransfer::ITEM_STATES]);
        $merchantOrderTableRowDataArray[MerchantOrderTransfer::ITEM_STATES] = $itemsStates;

        return $merchantOrderTableRowDataArray;
    }
}
