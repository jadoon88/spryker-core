<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductImageStorage\Dependency\Facade;

interface ProductImageStorageToProductImageInterface
{
    /**
     * @param int $idProductAbstract
     * @param int $idLocale
     *
     * @return array<\Generated\Shared\Transfer\ProductImageSetTransfer>
     */
    public function getCombinedAbstractImageSets($idProductAbstract, $idLocale);

    /**
     * @param int $idProductConcrete
     * @param int $idProductAbstract
     * @param int $idLocale
     *
     * @return array<\Generated\Shared\Transfer\ProductImageSetTransfer>
     */
    public function getCombinedConcreteImageSets($idProductConcrete, $idProductAbstract, $idLocale);
}
