<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\UtilSanitizeXss\Dependency\External;

interface UtilSanitizeToXssSanitizeInterface
{
    /**
     * @param string $text
     * @param array<string> $allowedAttributes
     * @param array<string> $allowedHtmlTags
     *
     * @return string
     */
    public function sanitize(string $text, array $allowedAttributes = [], array $allowedHtmlTags = []): string;
}
