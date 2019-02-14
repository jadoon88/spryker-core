<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShoppingListProductOptionConnector\Persistence;

use Orm\Zed\ShoppingList\Persistence\SpyShoppingListItemQuery;
use Orm\Zed\ShoppingListProductOptionConnector\Persistence\SpyShoppingListProductOption;
use Orm\Zed\ShoppingListProductOptionConnector\Persistence\SpyShoppingListProductOptionQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \Spryker\Zed\ShoppingListProductOptionConnector\ShoppingListProductOptionConnectorConfig getConfig()
 * @method \Spryker\Zed\ShoppingListProductOptionConnector\Persistence\ShoppingListProductOptionConnectorEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\ShoppingListProductOptionConnector\Persistence\ShoppingListProductOptionConnectorRepositoryInterface getRepository()
 */
class ShoppingListProductOptionConnectorPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\ShoppingListProductOptionConnector\Persistence\SpyShoppingListProductOptionQuery
     */
    public function createSpyShoppingListProductOptionQuery(): SpyShoppingListProductOptionQuery
    {
        return SpyShoppingListProductOptionQuery::create();
    }

    /**
     * @return \Orm\Zed\ShoppingListProductOptionConnector\Persistence\SpyShoppingListProductOption
     */
    public function createSpyShoppingListProductOption(): SpyShoppingListProductOption
    {
        return new SpyShoppingListProductOption();
    }

    /**
     * @return \Orm\Zed\ShoppingList\Persistence\SpyShoppingListItemQuery
     */
    public function createSpyShoppingListItemQuery(): SpyShoppingListItemQuery
    {
        return new SpyShoppingListItemQuery();
    }
}
