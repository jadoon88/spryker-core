<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\Flysystem\Model\Builder;

interface FlysystemStorageBuilderInterface
{

    /**
     * @return \League\Flysystem\Filesystem
     */
    public function build();

}
