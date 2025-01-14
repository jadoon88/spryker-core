<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Acl\Dependency\Facade;

use Generated\Shared\Transfer\UserTransfer;

interface AclToUserInterface
{
    /**
     * @param \Generated\Shared\Transfer\UserTransfer $user
     *
     * @return bool
     */
    public function isSystemUser(UserTransfer $user): bool;

    /**
     * @param string $username
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function getUserByUsername(string $username): UserTransfer;

    /**
     * @return bool
     */
    public function hasCurrentUser(): bool;

    /**
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function getCurrentUser(): UserTransfer;
}
