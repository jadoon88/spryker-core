<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MinimumOrderValueDataImport\Dependency\Facade;

use Generated\Shared\Transfer\StoreTransfer;

class MinimumOrderValueDataImportToStoreFacadeBridge implements MinimumOrderValueDataImportToStoreFacadeInterface
{
    /**
     * @var \Spryker\Zed\Store\Business\StoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @param \Spryker\Zed\Store\Business\StoreFacadeInterface $storeFacade
     */
    public function __construct($storeFacade)
    {
        $this->storeFacade = $storeFacade;
    }

    /**
     * @param string $storeName
     *
     * @return \Generated\Shared\Transfer\StoreTransfer|null
     */
    public function getStoreByName(string $storeName): ?StoreTransfer
    {
        return $this->storeFacade->getStoreByName($storeName);
    }
}
