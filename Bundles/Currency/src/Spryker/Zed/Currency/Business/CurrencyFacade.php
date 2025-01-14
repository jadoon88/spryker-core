<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Currency\Business;

use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\QuoteValidationResponseTransfer;
use Generated\Shared\Transfer\StoreWithCurrencyTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Spryker\Zed\Currency\Business\CurrencyBusinessFactory getFactory()
 * @method \Spryker\Zed\Currency\Persistence\CurrencyRepositoryInterface getRepository()
 */
class CurrencyFacade extends AbstractFacade implements CurrencyFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $isoCode
     *
     * @return \Generated\Shared\Transfer\CurrencyTransfer
     */
    public function fromIsoCode($isoCode)
    {
        return $this->getFactory()->createCurrencyReader()->getByIsoCode($isoCode);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\CurrencyTransfer
     */
    public function getCurrent()
    {
        return $this->getFactory()->createCurrencyBuilder()->getCurrent();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CurrencyTransfer $currencyTransfer
     *
     * @return int
     */
    public function createCurrency(CurrencyTransfer $currencyTransfer)
    {
        return $this->getFactory()
            ->createCurrencyWriter()
            ->create($currencyTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idCurrency
     *
     * @throws \Spryker\Zed\Currency\Business\Model\Exception\CurrencyNotFoundException
     *
     * @return \Generated\Shared\Transfer\CurrencyTransfer
     */
    public function getByIdCurrency($idCurrency)
    {
        return $this->getFactory()->createCurrencyReader()->getByIdCurrency($idCurrency);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @throws \Spryker\Zed\Currency\Business\Model\Exception\CurrencyNotFoundException
     *
     * @return \Generated\Shared\Transfer\StoreWithCurrencyTransfer
     */
    public function getCurrentStoreWithCurrencies()
    {
        return $this->getFactory()->createCurrencyReader()->getCurrentStoreWithCurrencies();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @throws \Spryker\Zed\Currency\Business\Model\Exception\CurrencyNotFoundException
     *
     * @return array<\Generated\Shared\Transfer\StoreWithCurrencyTransfer>
     */
    public function getAllStoresWithCurrencies()
    {
        return $this->getFactory()->createCurrencyReader()->getAllStoresWithCurrencies();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\CurrencyTransfer
     */
    public function getDefaultCurrencyForCurrentStore()
    {
        return $this->getFactory()
            ->createCurrencyReader()
            ->getDefaultCurrencyForCurrentStore();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteValidationResponseTransfer
     */
    public function validateCurrencyInQuote(QuoteTransfer $quoteTransfer): QuoteValidationResponseTransfer
    {
        return $this->getFactory()->createQuoteValidator()->validate($quoteTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $isoCode
     *
     * @return \Generated\Shared\Transfer\CurrencyTransfer|null
     */
    public function findCurrencyByIsoCode(string $isoCode): ?CurrencyTransfer
    {
        return $this->getRepository()
            ->findCurrencyByIsoCode($isoCode);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<string> $isoCodes
     *
     * @return array<\Generated\Shared\Transfer\CurrencyTransfer>
     */
    public function getCurrencyTransfersByIsoCodes(array $isoCodes): array
    {
        return $this->getFactory()
            ->createCurrencyBulkReader()
            ->getCurrencyTransfersByIsoCodes($isoCodes);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idStore
     *
     * @return \Generated\Shared\Transfer\StoreWithCurrencyTransfer
     */
    public function getStoreWithCurrenciesByIdStore(int $idStore): StoreWithCurrencyTransfer
    {
        return $this->getFactory()
            ->createCurrencyReader()
            ->getStoreWithCurrenciesByIdStore($idStore);
    }
}
