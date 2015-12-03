<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\CartCheckoutConnector\Business;

use Generated\Zed\Ide\FactoryAutoCompletion\CartCheckoutConnectorBusiness;
use SprykerEngine\Zed\Kernel\Business\AbstractBusinessDependencyContainer;
use SprykerFeature\Zed\CartCheckoutConnector\ProductOptionCheckoutConnectorConfig;

/**
 * @method CartCheckoutConnectorBusiness getFactory()
 * @method ProductOptionCheckoutConnectorConfig getConfig()
 */
class CartCheckoutConnectorDependencyContainer extends AbstractBusinessDependencyContainer
{

    public function createCartOrderHydrator()
    {
        return new CartOrderHydrator();
    }

}
