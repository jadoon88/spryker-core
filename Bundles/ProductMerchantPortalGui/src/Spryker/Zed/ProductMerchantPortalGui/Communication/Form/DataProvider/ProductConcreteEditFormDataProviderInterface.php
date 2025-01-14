<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductMerchantPortalGui\Communication\Form\DataProvider;

interface ProductConcreteEditFormDataProviderInterface
{
    /**
     * @param int $idProductConcrete
     *
     * @return array<mixed>
     */
    public function getData(int $idProductConcrete): array;

    /**
     * @return array<array<string>>
     */
    public function getOptions(): array;
}
