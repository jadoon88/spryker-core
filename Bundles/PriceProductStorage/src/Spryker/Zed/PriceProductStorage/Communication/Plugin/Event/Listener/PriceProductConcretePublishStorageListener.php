<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductStorage\Communication\Plugin\Event\Listener;

use Spryker\Zed\Event\Dependency\Plugin\EventBulkHandlerInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\PriceProduct\Dependency\PriceProductEvents;
use Spryker\Zed\PropelOrm\Business\Transaction\DatabaseTransactionHandlerTrait;

/**
 * @deprecated Use {@link \Spryker\Zed\PriceProductStorage\Communication\Plugin\Event\Listener\PriceProductConcreteStoragePublishListener}
 *   and {@link \Spryker\Zed\PriceProductStorage\Communication\Plugin\Event\Listener\PriceProductConcreteStorageUnpublishListener} instead.
 *
 * @method \Spryker\Zed\PriceProductStorage\Persistence\PriceProductStorageQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\PriceProductStorage\Communication\PriceProductStorageCommunicationFactory getFactory()
 * @method \Spryker\Zed\PriceProductStorage\Business\PriceProductStorageFacadeInterface getFacade()
 * @method \Spryker\Zed\PriceProductStorage\PriceProductStorageConfig getConfig()
 */
class PriceProductConcretePublishStorageListener extends AbstractPlugin implements EventBulkHandlerInterface
{
    use DatabaseTransactionHandlerTrait;

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\EventEntityTransfer[] $eventEntityTransfers
     * @param string $eventName
     *
     * @return void
     */
    public function handleBulk(array $eventEntityTransfers, $eventName)
    {
        $this->preventTransaction();
        $concreteIds = $this->getFactory()->getEventBehaviorFacade()->getEventTransferIds($eventEntityTransfers);

        if (
            $eventName === PriceProductEvents::ENTITY_SPY_PRICE_TYPE_DELETE ||
            $eventName === PriceProductEvents::ENTITY_SPY_PRICE_PRODUCT_DELETE ||
            $eventName === PriceProductEvents::PRICE_CONCRETE_UNPUBLISH
        ) {
            $this->getFacade()->unpublishPriceProductConcrete($concreteIds);

            return;
        }

        $this->getFacade()->publishPriceProductConcrete($concreteIds);
    }
}
