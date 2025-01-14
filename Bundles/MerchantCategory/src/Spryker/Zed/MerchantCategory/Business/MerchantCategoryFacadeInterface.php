<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantCategory\Business;

use Generated\Shared\Transfer\MerchantCategoryCriteriaTransfer;
use Generated\Shared\Transfer\MerchantCategoryResponseTransfer;

interface MerchantCategoryFacadeInterface
{
    /**
     * Specification:
     * - Returns transfer with list of merchant categories by provided criteria.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantCategoryCriteriaTransfer $merchantCategoryCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantCategoryResponseTransfer
     */
    public function get(MerchantCategoryCriteriaTransfer $merchantCategoryCriteriaTransfer): MerchantCategoryResponseTransfer;

    /**
     * Specification:
     * - Triggers `MerchantCategory.merchant_category.publish` event with the list of merchant categories Ids and merchant Ids
     * by provided Category Ids in the collection of EventEntityTransfer.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $transfers
     *
     * @return void
     */
    public function publishMerchantCategoryEventsByCategoryEvents(array $transfers): void;

    /**
     * Specification:
     * - Deletes items from `spy_merchant_category` by MerchantCategoryCriteriaTransfer.
     * - Requires `MerchantCategoryCriteriaTransfer::fkCategory` to be provided.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantCategoryCriteriaTransfer $merchantCategoryCriteriaTransfer
     *
     * @return void
     */
    public function delete(MerchantCategoryCriteriaTransfer $merchantCategoryCriteriaTransfer): void;
}
