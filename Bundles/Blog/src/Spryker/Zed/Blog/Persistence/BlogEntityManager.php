<?php
/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Blog\Persistence;

use Generated\Shared\Transfer\BlogCommentTransfer;
use Generated\Shared\Transfer\BlogTransfer;
use Generated\Shared\Transfer\SpyBlogCommentEntityTransfer;
use Generated\Shared\Transfer\SpyBlogEntityTransfer;
use Orm\Zed\Blog\Persistence\SpyBlog;
use Spryker\Shared\Kernel\Transfer\TransferInterface;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;
use Spryker\Zed\Kernel\Persistence\Repository\EntityManagerInterface;
use Spryker\Zed\PropelOrm\Business\Transaction\DatabaseTransactionHandlerTrait;

/**
 * @method \Spryker\Zed\Blog\Persistence\BlogPersistenceFactory getFactory()
 *
 * Should be resolvable by business factory
 *
 */
class BlogEntityManager extends AbstractEntityManager implements BlogEntityManagerInterface, EntityManagerInterface
{
    use DatabaseTransactionHandlerTrait;

    /**
     * @param \Generated\Shared\Transfer\SpyBlogEntityTransfer $blogEntityTransfer
     *
     * @return \Generated\Shared\Transfer\SpyBlogEntityTransfer
     */
    public function saveBlog(SpyBlogEntityTransfer $blogEntityTransfer)
    {
        return $this->handleDatabaseTransaction(function () use ($blogEntityTransfer) {
            return $this->save($blogEntityTransfer);
        });
    }

    /**
     * @param \Generated\Shared\Transfer\SpyBlogCommentEntityTransfer $blogCommentEntityTransfer
     *
     * @return \Generated\Shared\Transfer\SpyBlogCommentEntityTransfer
     */
    public function saveBlogComment(SpyBlogCommentEntityTransfer $blogCommentEntityTransfer)
    {
        return $this->handleDatabaseTransaction(function () use ($blogCommentEntityTransfer) {
            return $this->save($blogCommentEntityTransfer);
        });
    }

    public function deleteBlog(SpyBlogEntityTransfer $blogEntityTransfer)
    {
        //delete by this blog transfer
    }
}
