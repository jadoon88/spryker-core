<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantUser\Business;

use Generated\Shared\Transfer\MerchantUserCriteriaTransfer;
use Generated\Shared\Transfer\MerchantUserResponseTransfer;
use Generated\Shared\Transfer\MerchantUserTransfer;
use Generated\Shared\Transfer\UserCriteriaTransfer;
use Generated\Shared\Transfer\UserPasswordResetRequestTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Spryker\Zed\MerchantUser\Business\MerchantUserBusinessFactory getFactory()
 * @method \Spryker\Zed\MerchantUser\Persistence\MerchantUserRepositoryInterface getRepository()
 * @method \Spryker\Zed\MerchantUser\Persistence\MerchantUserEntityManagerInterface getEntityManager()
 */
class MerchantUserFacade extends AbstractFacade implements MerchantUserFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $merchantUserTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantUserResponseTransfer
     */
    public function createMerchantUser(MerchantUserTransfer $merchantUserTransfer): MerchantUserResponseTransfer
    {
        return $this->getFactory()
            ->createMerchantUserCreator()
            ->create($merchantUserTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $merchantUserTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantUserResponseTransfer
     */
    public function updateMerchantUser(MerchantUserTransfer $merchantUserTransfer): MerchantUserResponseTransfer
    {
        return $this->getFactory()
            ->createMerchantUserUpdater()
            ->update($merchantUserTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $merchantUserTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantUserResponseTransfer
     */
    public function deleteMerchantUser(MerchantUserTransfer $merchantUserTransfer): MerchantUserResponseTransfer
    {
        return $this->getFactory()
            ->createMerchantUserDeleter()
            ->delete($merchantUserTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantUserCriteriaTransfer $merchantUserCriteriaTransfer
     *
     * @return void
     */
    public function disableMerchantUsers(MerchantUserCriteriaTransfer $merchantUserCriteriaTransfer): void
    {
        $this->getFactory()
            ->createMerchantUserUpdater()
            ->disable($merchantUserCriteriaTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantUserCriteriaTransfer $merchantUserCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantUserTransfer|null
     */
    public function findMerchantUser(MerchantUserCriteriaTransfer $merchantUserCriteriaTransfer): ?MerchantUserTransfer
    {
        return $this->getFactory()
            ->createMerchantUserReader()
            ->findOne($merchantUserCriteriaTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UserCriteriaTransfer $userCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UserTransfer|null
     */
    public function findUser(UserCriteriaTransfer $userCriteriaTransfer): ?UserTransfer
    {
        return $this->getFactory()
            ->getUserFacade()
            ->findUser($userCriteriaTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     */
    public function getCurrentMerchantUser(): MerchantUserTransfer
    {
        return $this->getFactory()
            ->createCurrentMerchantUserReader()
            ->getCurrentMerchantUser();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $merchantUserTransfer
     *
     * @return mixed
     */
    public function setCurrentMerchantUser(MerchantUserTransfer $merchantUserTransfer)
    {
        return $this->getFactory()
            ->getUserFacade()
            ->setCurrentUser($merchantUserTransfer->getUserOrFail());
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $merchantUserTransfer
     *
     * @return void
     */
    public function authenticateMerchantUser(MerchantUserTransfer $merchantUserTransfer): void
    {
        $this->getFactory()
            ->createMerchantUserAuthenticator()
            ->authenticateMerchantUser($merchantUserTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UserPasswordResetRequestTransfer $userPasswordResetRequestTransfer
     *
     * @return bool
     */
    public function requestPasswordReset(UserPasswordResetRequestTransfer $userPasswordResetRequestTransfer): bool
    {
        return $this->getFactory()
            ->getUserPasswordResetFacade()
            ->requestPasswordReset($userPasswordResetRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $token
     *
     * @return bool
     */
    public function isValidPasswordResetToken(string $token): bool
    {
        return $this->getFactory()
            ->getUserPasswordResetFacade()
            ->isValidPasswordResetToken($token);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $token
     * @param string $password
     *
     * @return bool
     */
    public function setNewPassword(string $token, string $password): bool
    {
        return $this->getFactory()
            ->getUserPasswordResetFacade()
            ->setNewPassword($token, $password);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function isValidPassword($password, $hash): bool
    {
        return $this->getFactory()->getUserFacade()->isValidPassword($password, $hash);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     * @param array<string> $roles
     *
     * @return array<string>
     */
    public function filterUserRoles(UserTransfer $userTransfer, array $roles): array
    {
        return $this->getFactory()->createBackofficeMerchantUserRoleFilter()->filterUserRoles($userTransfer, $roles);
    }
}
