<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Acl\Dependency\Facade;

use Generated\Shared\Transfer\UserTransfer;

class AclToUserBridge implements AclToUserInterface
{
    /**
     * @var \Spryker\Zed\User\Business\UserFacadeInterface
     */
    protected $userFacade;

    /**
     * @param \Spryker\Zed\User\Business\UserFacadeInterface $userFacade
     */
    public function __construct($userFacade)
    {
        $this->userFacade = $userFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $user
     *
     * @return bool
     */
    public function isSystemUser(UserTransfer $user): bool
    {
        return $this->userFacade->isSystemUser($user);
    }

    /**
     * @param string $username
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function getUserByUsername(string $username): UserTransfer
    {
        return $this->userFacade->getUserByUsername($username);
    }

    /**
     * @return bool
     */
    public function hasCurrentUser(): bool
    {
        return $this->userFacade->hasCurrentUser();
    }

    /**
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function getCurrentUser(): UserTransfer
    {
        return $this->userFacade->getCurrentUser();
    }
}
