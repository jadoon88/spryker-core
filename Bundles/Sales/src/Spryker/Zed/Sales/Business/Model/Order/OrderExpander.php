<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Sales\Business\Model\Order;

use ArrayObject;
use Generated\Shared\Transfer\ItemCollectionTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Sales\Dependency\Facade\SalesToCalculationInterface;

class OrderExpander implements OrderExpanderInterface
{
    /**
     * @var \Spryker\Zed\Sales\Dependency\Facade\SalesToCalculationInterface
     */
    protected $calculationFacade;

    /**
     * @var \Spryker\Zed\SalesExtension\Dependency\Plugin\SalesItemTransformerStrategyPluginInterface[]
     */
    protected $salesItemTransformerStrategyPlugins;

    /**
     * @param \Spryker\Zed\Sales\Dependency\Facade\SalesToCalculationInterface $calculationFacade
     * @param \Spryker\Zed\SalesExtension\Dependency\Plugin\SalesItemTransformerStrategyPluginInterface[] $salesItemTransformerStrategyPlugins
     */
    public function __construct(SalesToCalculationInterface $calculationFacade, array $salesItemTransformerStrategyPlugins)
    {
        $this->calculationFacade = $calculationFacade;
        $this->salesItemTransformerStrategyPlugins = $salesItemTransformerStrategyPlugins;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expandSalesOrder(QuoteTransfer $quoteTransfer)
    {
        $orderTransfer = new OrderTransfer();
        $orderTransfer->fromArray($quoteTransfer->toArray(), true);
        $orderTransfer->setItems($this->transformItems($quoteTransfer->getItems()));

        $this->groupOrderDiscountsByGroupKey($orderTransfer->getItems());
        $orderTransfer = $this->calculationFacade->recalculateOrder($orderTransfer);

        $quoteTransfer->fromArray($orderTransfer->toArray(), true);

        return $quoteTransfer;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $items
     *
     * @return \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[]
     */
    protected function transformItems(ArrayObject $items): ArrayObject
    {
        $transformedItemTransferArray = [];
        foreach ($items as $itemTransfer) {
            $transformedItemTransferCollection = $this->transformItemTransferPerStrategyPlugin($itemTransfer);
            $transformedItemTransferArray = array_merge($transformedItemTransferArray, $transformedItemTransferCollection->getItems()->getArrayCopy());
        }

        return new ArrayObject($transformedItemTransferArray);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\ItemCollectionTransfer
     */
    protected function transformItemTransferPerStrategyPlugin(ItemTransfer $itemTransfer): ItemCollectionTransfer
    {
        foreach ($this->salesItemTransformerStrategyPlugins as $salesItemTransformerStrategyPlugin) {
            if ($salesItemTransformerStrategyPlugin->isApplicable($itemTransfer)) {
                return $salesItemTransformerStrategyPlugin->transformItem($itemTransfer);
            }
        }

        return (new ItemCollectionTransfer())->addItem($itemTransfer);
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $itemCollection
     *
     * @return void
     */
    protected function groupOrderDiscountsByGroupKey(ArrayObject $itemCollection)
    {
        $calculatedItemDiscountsByGroupKey = [];
        $optionCalculatedDiscountsByGroupKey = [];
        foreach ($itemCollection as $itemTransfer) {
            if (!isset($calculatedItemDiscountsByGroupKey[$itemTransfer->getGroupKey()])) {
                $calculatedItemDiscountsByGroupKey[$itemTransfer->getGroupKey()] = (array)$itemTransfer->getCalculatedDiscounts();
            }
            $itemTransfer->setCalculatedDiscounts(
                $this->getGroupedCalculatedDiscounts($calculatedItemDiscountsByGroupKey, $itemTransfer->getGroupKey())
            );
            foreach ($itemTransfer->getProductOptions() as $productOptionTransfer) {
                if (!isset($optionCalculatedDiscountsByGroupKey[$itemTransfer->getGroupKey()])) {
                    $optionCalculatedDiscountsByGroupKey[$itemTransfer->getGroupKey()] = (array)$productOptionTransfer->getCalculatedDiscounts();
                }
                $productOptionTransfer->setCalculatedDiscounts(
                    $this->getGroupedCalculatedDiscounts($optionCalculatedDiscountsByGroupKey, $itemTransfer->getGroupKey())
                );
            }
        }
    }

    /**
     * @param array $calculatedDiscountsByGroupKey
     * @param string $groupKey
     *
     * @return \ArrayObject
     */
    protected function getGroupedCalculatedDiscounts(array &$calculatedDiscountsByGroupKey, $groupKey)
    {
        $discountCollection = $calculatedDiscountsByGroupKey[$groupKey];

        $appliedDiscounts = [];
        foreach ($discountCollection as $key => $discountTransfer) {
            $idDiscount = $discountTransfer->getIdDiscount();
            if (isset($appliedDiscounts[$idDiscount])) {
                continue;
            }

            $appliedDiscounts[$idDiscount] = $discountTransfer;
            unset($discountCollection[$key]);
        }
        $calculatedDiscountsByGroupKey[$groupKey] = $discountCollection;

        return new ArrayObject($appliedDiscounts);
    }
}
