<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductPageSearch\Communication\Plugin\Event\Listener;

use Spryker\Zed\Event\Dependency\Plugin\EventBulkHandlerInterface;

/**
 * @method \Spryker\Zed\ProductPageSearch\Persistence\ProductPageSearchQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\ProductPageSearch\Communication\ProductPageSearchCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductPageSearch\ProductPageSearchConfig getConfig()
 * @method \Spryker\Zed\ProductPageSearch\Business\ProductPageSearchFacadeInterface getFacade()
 */
class ProductPageImageSetProductImageSearchListener extends AbstractProductPageSearchListener implements EventBulkHandlerInterface
{
    /**
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     * @param string $eventName
     *
     * @return void
     */
    public function handleBulk(array $eventEntityTransfers, $eventName)
    {
        $productImageSetToProductImageIds = $this->getFactory()->getEventBehaviorFacade()->getEventTransferIds($eventEntityTransfers);
        $productAbstractIds = $this->getQueryContainer()->queryProductAbstractIdsByProductImageSetToProductImageIds($productImageSetToProductImageIds)->find()->getData();

        $this->publish($productAbstractIds);
    }
}
