<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Price\Business\Model;

use Generated\Shared\Transfer\PriceProductTransfer;
use SprykerFeature\Zed\Price\Persistence\Propel\SpyPriceType;
use Propel\Runtime\Exception\PropelException;

interface WriterInterface
{
    /**
     * @param string $name
     * @return SpyPriceType
     * @throws \Exception
     * @throws PropelException
     */
    public function createPriceType($name);

    /**
     * @param PriceProductTransfer $transferPriceProduct
     */
    public function setPriceForProduct(PriceProductTransfer $transferPriceProduct);

    /**
     * @param PriceProductTransfer $transferPriceProduct
     */
    public function createPriceForProduct(PriceProductTransfer $transferPriceProduct);
}
