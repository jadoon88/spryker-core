<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductListStorage\Plugin\ProductStorageExtension;

use Spryker\Client\Kernel\AbstractPlugin;
use Spryker\Client\ProductStorageExtension\Dependency\Plugin\ProductAbstractRestrictionFilterPluginInterface;

/**
 * @method \Spryker\Client\ProductListStorage\ProductListStorageClientInterface getClient()
 */
class ProductListStorageAbstractRestrictionFilterPlugin extends AbstractPlugin implements ProductAbstractRestrictionFilterPluginInterface
{
    /**
     * {@inheritdoc}
     * - Filters abstract product ids based on white and blacklists.
     *
     * @api
     *
     * @param int[] $productAbstractIds
     *
     * @return int[]
     */
    public function filter(array $productAbstractIds): array
    {
        return $this->getClient()->filterRestrictedAbstractProducts($productAbstractIds);
    }
}
