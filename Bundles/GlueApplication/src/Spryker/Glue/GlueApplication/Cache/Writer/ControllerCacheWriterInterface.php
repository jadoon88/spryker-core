<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Cache\Writer;

interface ControllerCacheWriterInterface
{
    /**
     * @return void
     */
    public function cache(): void;
}
