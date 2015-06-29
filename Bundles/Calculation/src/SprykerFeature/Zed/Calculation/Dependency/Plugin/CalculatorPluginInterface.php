<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Calculation\Dependency\Plugin;

use Generated\Shared\Calculation\OrderInterface;

interface CalculatorPluginInterface
{
    /**
     * @param OrderInterface $calculableContainer
     */
    public function recalculate(OrderInterface $calculableContainer);
}
