<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MerchantStock;

use Codeception\Actor;
use Orm\Zed\MerchantStock\Persistence\SpyMerchantStock;
use Spryker\Zed\Stock\Business\StockFacadeInterface;

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class MerchantStockBusinessTester extends Actor
{
    use _generated\MerchantStockBusinessTesterActions;

    /**
     * @param int $idMerchant
     * @param int $idStock
     *
     * @return \Orm\Zed\MerchantStock\Persistence\SpyMerchantStock
     */
    public function haveMerchantStock(int $idMerchant, int $idStock): SpyMerchantStock
    {
        $merchantStockEntity = (new SpyMerchantStock())
            ->setFkMerchant($idMerchant)
            ->setFkStock($idStock);

        $merchantStockEntity->save();

        return $merchantStockEntity;
    }

    /**
     * @return \Spryker\Zed\Stock\Business\StockFacadeInterface
     */
    public function getStockFacade(): StockFacadeInterface
    {
        return $this->getLocator()->stock()->facade();
    }
}
