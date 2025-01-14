<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\PriceProductOfferStorage\Communication\Plugin\Event\Listener;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\EventEntityTransfer;
use Generated\Shared\Transfer\PriceProductOfferTransfer;
use Generated\Shared\Transfer\PriceProductTransfer;
use Generated\Shared\Transfer\ProductOfferTransfer;
use Spryker\Client\Kernel\Container;
use Spryker\Client\Queue\QueueDependencyProvider;
use Spryker\Zed\PriceProductOffer\Dependency\PriceProductOfferEvents;
use Spryker\Zed\PriceProductOfferStorage\Communication\Plugin\Event\Listener\PriceProductOfferStoragePublishListener;
use Spryker\Zed\PriceProductOfferStorage\Communication\Plugin\Event\Listener\ProductUnpublishListener;
use Spryker\Zed\Product\Dependency\ProductEvents;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group PriceProductOfferStorage
 * @group Communication
 * @group Plugin
 * @group Event
 * @group Listener
 * @group ProductUnpublishListenerTest
 * Add your own group annotations below this line
 */
class ProductUnpublishListenerTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\PriceProductOfferStorage\PriceProductOfferStorageTester
     */
    protected $tester;

    /**
     * @var \Spryker\Zed\PriceProductOfferStorage\Communication\Plugin\Event\Listener\PriceProductOfferStoragePublishListener
     */
    protected $priceProductOfferStoragePublishListener;

    /**
     * @var \Spryker\Zed\PriceProductOfferStorage\Communication\Plugin\Event\Listener\ProductUnpublishListener
     */
    protected $productUnpublishListener;

    /**
     * @var \Generated\Shared\Transfer\PriceProductOfferTransfer
     */
    protected $priceProductOfferTransfer;

    /**
     * @var \Generated\Shared\Transfer\ProductConcreteTransfer
     */
    protected $productConcreteTransfer;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tester->setDependency(QueueDependencyProvider::QUEUE_ADAPTERS, function (Container $container) {
            return [
                $container->getLocator()->rabbitMq()->client()->createQueueAdapter(),
            ];
        });

        $this->priceProductOfferStoragePublishListener = new PriceProductOfferStoragePublishListener();
        $this->productUnpublishListener = new ProductUnpublishListener();

        $this->productConcreteTransfer = $this->tester->haveProduct();
        $productOfferTransfer = $this->tester->haveProductOffer([
            ProductOfferTransfer::FK_MERCHANT => $this->tester->haveMerchant()->getIdMerchant(),
            ProductOfferTransfer::CONCRETE_SKU => $this->productConcreteTransfer->getSku(),
        ]);

        $this->priceProductOfferTransfer = $this->tester->havePriceProductOffer([
            PriceProductOfferTransfer::FK_PRODUCT_OFFER => $productOfferTransfer->getIdProductOffer(),
            PriceProductTransfer::SKU_PRODUCT => $this->productConcreteTransfer->getSku(),
            PriceProductTransfer::SKU_PRODUCT_ABSTRACT => $this->productConcreteTransfer->getAbstractSku(),
        ]);
    }

    /**
     * @return void
     */
    public function testProductOfferStoragePublishListener(): void
    {
        //Arrange
        $eventPriceProductOfferStorageTransfers = [
            (new EventEntityTransfer())->setId($this->priceProductOfferTransfer->getIdPriceProductOffer()),
        ];
        $eventProductUnpublishTransfers = [
            (new EventEntityTransfer())
                ->setId($this->productConcreteTransfer->getIdProductConcrete()),
        ];

        //Act
        $this->priceProductOfferStoragePublishListener->handleBulk(
            $eventPriceProductOfferStorageTransfers,
            PriceProductOfferEvents::ENTITY_SPY_PRICE_PRODUCT_OFFER_PUBLISH,
        );

        $this->productUnpublishListener->handleBulk(
            $eventProductUnpublishTransfers,
            ProductEvents::ENTITY_SPY_PRODUCT_UPDATE,
        );

        $countPriceProductOfferStorageEntities = $this->tester->getCountPriceProductOfferStorageCount($this->productConcreteTransfer->getIdProductConcrete());

        //Assert
        $this->assertSame(0, $countPriceProductOfferStorageEntities);
    }

    /**
     * @return void
     */
    protected function _after(): void
    {
        parent::_after();

        $this->tester->deletePriceProductOfferStorageEntities();
    }
}
