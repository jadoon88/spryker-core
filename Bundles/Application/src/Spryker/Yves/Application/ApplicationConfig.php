<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\Application;

use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Shared\Kernel\KernelConstants;
use Spryker\Yves\Kernel\AbstractBundleConfig;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Shared\Application\ApplicationConfig getSharedConfig()
 */
class ApplicationConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    protected const HEADER_X_FRAME_OPTIONS_VALUE = 'SAMEORIGIN';

    /**
     * @var string
     */
    protected const HEADER_CONTENT_SECURITY_POLICY_VALUE = 'frame-ancestors \'self\'; sandbox allow-downloads allow-forms allow-modals allow-pointer-lock allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts allow-top-navigation; base-uri \'self\'; form-action \'self\'';

    /**
     * @var string
     */
    protected const HEADER_X_CONTENT_TYPE_OPTIONS_VALUE = 'nosniff';

    /**
     * @var string
     */
    protected const HEADER_X_XSS_PROTECTION_VALUE = '1; mode=block';

    /**
     * @var string
     */
    protected const HEADER_REFERRER_POLICY_VALUE = 'same-origin';

    /**
     * @deprecated {@link https://www.w3.org/TR/permissions-policy-1/#introduction}
     *
     * @var string
     */
    protected const HEADER_FEATURE_POLICY_VALUE = '';

    /**
     * @var string
     */
    protected const HEADER_PERMISSION_POLICY_VALUE = '';

    /**
     * @api
     *
     * @return string
     */
    public function getHostName()
    {
        return $this->get(ApplicationConstants::HOST_YVES);
    }

    /**
     * @api
     *
     * @return bool
     */
    public function isSslEnabled()
    {
        return $this->get(ApplicationConstants::YVES_SSL_ENABLED, true);
    }

    /**
     * @api
     *
     * @return array<string>
     */
    public function getSslExcludedResources()
    {
        return $this->get(ApplicationConstants::YVES_SSL_EXCLUDED, []);
    }

    /**
     * @api
     *
     * @return array<string>
     */
    public function getTrustedProxies()
    {
        return $this->get(ApplicationConstants::YVES_TRUSTED_PROXIES, []);
    }

    /**
     * @api
     *
     * @return int
     */
    public function getTrustedHeader(): int
    {
        return $this->get(
            ApplicationConstants::YVES_TRUSTED_HEADER,
            Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO,
        );
    }

    /**
     * @api
     *
     * @return array<string>
     */
    public function getTrustedHosts()
    {
        return $this->get(ApplicationConstants::YVES_TRUSTED_HOSTS, []);
    }

    /**
     * @api
     *
     * @return array<string, string>
     */
    public function getSecurityHeaders(): array
    {
        $headers = [
            'X-Frame-Options' => static::HEADER_X_FRAME_OPTIONS_VALUE,
            'Content-Security-Policy' => static::HEADER_CONTENT_SECURITY_POLICY_VALUE,
            'X-Content-Type-Options' => static::HEADER_X_CONTENT_TYPE_OPTIONS_VALUE,
            'X-XSS-Protection' => static::HEADER_X_XSS_PROTECTION_VALUE,
            'Referrer-Policy' => static::HEADER_REFERRER_POLICY_VALUE,
            'Permissions-Policy' => static::HEADER_PERMISSION_POLICY_VALUE,
        ];

        $headers = $this->addFeaturePolicyHeader($headers);

        return $headers;
    }

    /**
     * @deprecated {@link https://www.w3.org/TR/permissions-policy-1/#introduction}
     *
     * @param array<string> $headers
     *
     * @return array<string>
     */
    protected function addFeaturePolicyHeader(array $headers): array
    {
        $headers['Feature-Policy'] = static::HEADER_FEATURE_POLICY_VALUE;

        return $headers;
    }

    /**
     * @api
     *
     * @return bool
     */
    public function isDebugModeEnabled(): bool
    {
        return $this->getSharedConfig()->isDebugModeEnabled();
    }

    /**
     * @api
     *
     * @return array<int, string>
     */
    public function getDomainWhitelist(): array
    {
        return $this->get(KernelConstants::DOMAIN_WHITELIST, []);
    }
}
