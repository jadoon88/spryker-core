<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MinimumOrderValue\Persistence;

use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\MinimumOrderValueTransfer;
use Generated\Shared\Transfer\MinimumOrderValueTypeTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \Spryker\Zed\MinimumOrderValue\Persistence\MinimumOrderValuePersistenceFactory getFactory()
 */
class MinimumOrderValueEntityManager extends AbstractEntityManager implements MinimumOrderValueEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\MinimumOrderValueTypeTransfer $minimumOrderValueTypeTransfer
     *
     * @return \Generated\Shared\Transfer\MinimumOrderValueTypeTransfer
     */
    public function saveMinimumOrderValueType(
        MinimumOrderValueTypeTransfer $minimumOrderValueTypeTransfer
    ): MinimumOrderValueTypeTransfer {
        $minimumOrderValueTypeTransfer->requireKey()->requireGroup();

        $minimumOrderValueTypeEntity = $this->getFactory()
            ->createMinimumOrderValueTypeQuery()
            ->filterByKey($minimumOrderValueTypeTransfer->getKey())
            ->findOneOrCreate();

        $minimumOrderValueTypeEntity
            ->setThresholdGroup($minimumOrderValueTypeTransfer->getGroup())
            ->save();

        $this->getFactory()
            ->createMinimumOrderValueMapper()
            ->mapMinimumOrderValueTypeTransfer(
                $minimumOrderValueTypeEntity,
                $minimumOrderValueTypeTransfer
            );

        return $minimumOrderValueTypeTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MinimumOrderValueTypeTransfer $minimumOrderValueTypeTransfer
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param \Generated\Shared\Transfer\CurrencyTransfer $currencyTransfer
     * @param int $value
     * @param int|null $fee
     *
     * @return \Generated\Shared\Transfer\MinimumOrderValueTransfer
     */
    public function setStoreThreshold(
        MinimumOrderValueTypeTransfer $minimumOrderValueTypeTransfer,
        StoreTransfer $storeTransfer,
        CurrencyTransfer $currencyTransfer,
        int $value,
        ?int $fee = null
    ): MinimumOrderValueTransfer {
        $minimumOrderValueTypeTransfer->requireIdMinimumOrderValueType()->requireGroup();
        $storeTransfer->requireIdStore();
        $currencyTransfer->requireIdCurrency();

        $minimumOrderValueEntity = $this->getFactory()
            ->createMinimumOrderValueQuery()
            ->filterByFkStore($storeTransfer->getIdStore())
            ->filterByFkCurrency($currencyTransfer->getIdCurrency())
            ->filterByThresholdGroup($minimumOrderValueTypeTransfer->getGroup())
            ->findOneOrCreate();

        $minimumOrderValueEntity
            ->setValue($value)
            ->setFee($fee)
            ->setFkMinOrderValueType(
                $minimumOrderValueTypeTransfer->getIdMinimumOrderValueType()
            )->save();

        $minimumOrderValueTransfer = $this->getFactory()
            ->createMinimumOrderValueMapper()
            ->mapMinimumOrderValueTransfer(
                $minimumOrderValueEntity,
                new MinimumOrderValueTransfer()
            );

        return $minimumOrderValueTransfer;
    }
}
