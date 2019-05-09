<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ContentProduct\Dependency\Client;

use Generated\Shared\Transfer\ContentTypeContextTransfer;

interface ContentProductToContentStorageClientInterface
{
    /**
     * @param int $id
     * @param string $locale
     *
     * @return \Generated\Shared\Transfer\ContentTypeContextTransfer|null
     */
    public function findContentTypeContext(int $id, string $locale): ?ContentTypeContextTransfer;
}