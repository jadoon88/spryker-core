<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Product\Business\Reader;

use Generated\Shared\Transfer\ProductConcreteTransfer;

interface ProductConcreteReaderInterface
{
    /**
     * @param int $productConcreteId
     *
     * @return \Generated\Shared\Transfer\ProductConcreteTransfer
     */
    public function readProductConcreteMergedWithProductAbstractById(int $productConcreteId): ProductConcreteTransfer;
}
