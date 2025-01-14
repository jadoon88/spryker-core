<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\Language;

use Negotiation\AcceptLanguage;
use Negotiation\LanguageNegotiator;
use Spryker\Glue\GlueApplication\Dependency\Client\GlueApplicationToStoreClientInterface;

/**
 * @deprecated Will be removed without replacement.
 */
class LanguageNegotiation implements LanguageNegotiationInterface
{
    /**
     * @var \Spryker\Glue\GlueApplication\Dependency\Client\GlueApplicationToStoreClientInterface
     */
    protected $storeClient;

    /**
     * @var \Negotiation\LanguageNegotiator
     */
    protected $negotiator;

    /**
     * @param \Spryker\Glue\GlueApplication\Dependency\Client\GlueApplicationToStoreClientInterface $storeClient
     * @param \Negotiation\LanguageNegotiator $negotiator
     */
    public function __construct(GlueApplicationToStoreClientInterface $storeClient, LanguageNegotiator $negotiator)
    {
        $this->storeClient = $storeClient;
        $this->negotiator = $negotiator;
    }

    /**
     * @param string $acceptLanguage
     *
     * @return string
     */
    public function getLanguageIsoCode(string $acceptLanguage): string
    {
        $storeTransfer = $this->storeClient->getCurrentStore();
        $storeLocaleCodes = $storeTransfer->getAvailableLocaleIsoCodes();

        if (!$acceptLanguage) {
            return array_shift($storeLocaleCodes);
        }

        $language = $this->findAcceptedLanguage($acceptLanguage, $storeLocaleCodes);
        if (!$language) {
            return array_shift($storeLocaleCodes);
        }

        return $storeLocaleCodes[$language->getType()];
    }

    /**
     * @param string $acceptLanguage
     * @param array $storeLocaleCodes
     *
     * @return \Negotiation\AcceptLanguage|null
     */
    protected function findAcceptedLanguage(string $acceptLanguage, array $storeLocaleCodes): ?AcceptLanguage
    {
        /** @var \Negotiation\AcceptLanguage $acceptedLanguage */
        $acceptedLanguage = $this->negotiator->getBest($acceptLanguage, array_keys($storeLocaleCodes));

        return $acceptedLanguage;
    }
}
