<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductLabelSearch\Persistence;

use Orm\Zed\ProductLabel\Persistence\Map\SpyProductLabelProductAbstractTableMap;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\ProductLabelSearch\Persistence\ProductLabelSearchPersistenceFactory getFactory()
 */
class ProductLabelSearchRepository extends AbstractRepository implements ProductLabelSearchRepositoryInterface
{
    /**
     * @param array<int> $productAbstractIds
     *
     * @return array<\Generated\Shared\Transfer\SpyProductLabelEntityTransfer>
     */
    public function getProductLabelsByIdProductAbstractIn(array $productAbstractIds): array
    {
        $query = $this->getFactory()
            ->getPropelProductLabelQuery()
            ->filterByIsActive(true)
            ->innerJoinWithSpyProductLabelProductAbstract()
            ->useSpyProductLabelProductAbstractQuery()
                ->filterByFkProductAbstract_In($productAbstractIds)
            ->endUse();

        return $this->buildQueryFromCriteria($query)->find();
    }

    /**
     * @param array<int> $productLabelIds
     *
     * @return array<int>
     */
    public function getProductAbstractIdsByProductLabelIds(array $productLabelIds): array
    {
        return $this->getFactory()
            ->createSpyProductLabelProductAbstractQuery()
            ->filterByFkProductLabel_In($productLabelIds)
            ->select(SpyProductLabelProductAbstractTableMap::COL_FK_PRODUCT_ABSTRACT)
            ->distinct()
            ->find()
            ->getData();
    }
}
