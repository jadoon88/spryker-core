<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Sales\Business\Order;

use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Shared\Shipment\ShipmentConstants;
use Spryker\Zed\Sales\Business\Model\Order\OrderReader as OrderReaderWithoutMultiShippingAddress;
use Spryker\Zed\Sales\Persistence\SalesQueryContainerInterface;

class OrderReader extends OrderReaderWithoutMultiShippingAddress
{
    /**
     * @var \Spryker\Zed\Sales\Business\Order\OrderHydratorOrderDataBCForMultiShipmentAdapterInterface
     */
    protected $orderDataBCForMultiShipmentAdapter;

    /**
     * @param \Spryker\Zed\Sales\Persistence\SalesQueryContainerInterface $queryContainer
     * @param \Spryker\Zed\Sales\Business\Order\OrderHydratorInterface $orderHydrator
     * @param \Spryker\Zed\Sales\Business\Order\OrderHydratorOrderDataBCForMultiShipmentAdapterInterface $orderDataBCForMultiShipmentAdapter
     */
    public function __construct(
        SalesQueryContainerInterface $queryContainer,
        OrderHydratorInterface $orderHydrator,
        OrderHydratorOrderDataBCForMultiShipmentAdapterInterface $orderDataBCForMultiShipmentAdapter
    ) {
        parent::__construct($queryContainer, $orderHydrator);

        $this->orderDataBCForMultiShipmentAdapter = $orderDataBCForMultiShipmentAdapter;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\OrderTransfer|null
     */
    public function findOrderByIdSalesOrder(int $idSalesOrder): ?OrderTransfer
    {
        $orderEntity = $this->queryContainer
            ->querySalesOrderDetailsWithoutShippingAddress($idSalesOrder)
            ->findOne();

        if ($orderEntity === null) {
            return null;
        }

        /**
         * @deprecated Will be removed in next major release.
         */
        $orderEntity = $this->orderDataBCForMultiShipmentAdapter->adapt($orderEntity);
        $orderEntity = $this->sanitizeOrderShipmentExpense($orderEntity);

        return $this->orderHydrator->hydrateOrderTransferFromPersistenceBySalesOrder($orderEntity);
    }

    /**
     * @deprecated Will be removed in next major release.
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     *
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrder
     */
    protected function sanitizeOrderShipmentExpense(SpySalesOrder $orderEntity): ?SpySalesOrder
    {
        $orderExpensesCollection = $orderEntity->getExpenses();
        foreach ($orderExpensesCollection as $key => $expenseEntity) {
            if ($expenseEntity->getType() !== ShipmentConstants::SHIPMENT_EXPENSE_TYPE) {
                continue;
            }

            $orderExpensesCollection->offsetUnset($key);
        }

        return $orderEntity;
    }
}
