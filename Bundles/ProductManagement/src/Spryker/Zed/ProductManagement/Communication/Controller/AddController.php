<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductManagement\Communication\Controller;

use Generated\Shared\Transfer\ProductAbstractTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Category\Business\Exception\CategoryUrlExistsException;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Spryker\Zed\Product\Business\Exception\ProductAbstractExistsException;
use Spryker\Zed\ProductManagement\ProductManagementConfig;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\ProductManagement\Business\ProductManagementFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductManagement\Communication\ProductManagementCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductManagement\Persistence\ProductManagementQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\ProductManagement\Persistence\ProductManagementRepositoryInterface getRepository()
 */
class AddController extends AbstractController
{
    /**
     * @var string
     */
    public const PARAM_ID_PRODUCT_ABSTRACT = 'id-product-abstract';

    /**
     * @var string
     */
    protected const PARAM_ID_PRODUCT = 'id-product';

    /**
     * @var string
     */
    protected const PARAM_PRICE_DIMENSION = 'price-dimension';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function indexAction(Request $request)
    {
        $dataProvider = $this->getFactory()->createProductFormAddDataProvider();

        $type = (string)$request->query->get('type');

        /** @var array|null $priceDimension */
        $priceDimension = $request->query->get(static::PARAM_PRICE_DIMENSION);
        $form = $this
            ->getFactory()
            ->createProductFormAdd(
                $dataProvider->getData($priceDimension),
                $dataProvider->getOptions(),
            )
            ->handleRequest($request);

        $localeProvider = $this->getFactory()->createLocaleProvider();

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $productAbstractTransfer = $this->getFactory()
                    ->createProductFormTransferGenerator()
                    ->buildProductAbstractTransfer($form, null);

                $concreteProductCollection = $this->createProductConcreteCollection(
                    (string)$type,
                    $productAbstractTransfer,
                    $form,
                );

                $idProductAbstract = $this->getFactory()
                    ->getProductFacade()
                    ->addProduct($productAbstractTransfer, $concreteProductCollection);

                $this->addSuccessMessage('The product [%s] was added successfully.', [
                    '%s' => $productAbstractTransfer->getSku(),
                ]);

                return $this->createRedirectResponseAfterAdd($idProductAbstract, $request);
            } catch (CategoryUrlExistsException $exception) {
                $this->addErrorMessage($exception->getMessage());
            } catch (ProductAbstractExistsException $exception) {
                $this->addErrorMessage($exception->getMessage());
            }
        }

        return $this->viewResponse([
            'form' => $form->createView(),
            'currentLocale' => $this->getFactory()->getLocaleFacade()->getCurrentLocale()->getLocaleName(),
            'concreteProductCollection' => [],
            'localeCollection' => $localeProvider->getLocaleCollection(),
            'attributeLocaleCollection' => $localeProvider->getLocaleCollection(true),
            'productFormAddTabs' => $this->getFactory()->createProductFormAddTabs()->createView(),
            'type' => $type,
        ]);
    }

    /**
     * @param int $idProductAbstract
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function createRedirectResponseAfterAdd(int $idProductAbstract, Request $request)
    {
        $params = $request->query->all();
        $params[static::PARAM_ID_PRODUCT_ABSTRACT] = $idProductAbstract;

        return $this->redirectResponse(
            urldecode(Url::generate('/product-management/edit', $params)->build()),
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function bundledProductTableAction(Request $request)
    {
        $idProductConcrete = $this->castId($request->get('id-product-concrete'));

        $bundledProductTable = $this->getFactory()
            ->createBundledProductTable($idProductConcrete);

        return $this->jsonResponse(
            $bundledProductTable->fetchData(),
        );
    }

    /**
     * @param array $keys
     * @param array $attributes
     *
     * @return array
     */
    protected function getAttributeValues(array $keys, array $attributes)
    {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $attributes[$key];
        }

        return $values;
    }

    /**
     * @param string $type
     * @param \Generated\Shared\Transfer\ProductAbstractTransfer $productAbstractTransfer
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return array<\Generated\Shared\Transfer\ProductConcreteTransfer>
     */
    protected function createProductConcreteCollection(
        $type,
        ProductAbstractTransfer $productAbstractTransfer,
        FormInterface $form
    ) {
        if ($type === ProductManagementConfig::PRODUCT_TYPE_BUNDLE) {
            $productConcreteTransfer = $this->copyProductAbstractToProductConcrete($productAbstractTransfer);

            return [$productConcreteTransfer];
        }

        $productSuperAttributes = $this->getFactory()
            ->createProductAttributeReader()
            ->getProductSuperAttributesIndexedByAttributeKey();

        $attributeValues = $this->getFactory()
            ->createProductFormTransferGenerator()
            ->generateVariantAttributeArrayFromData($form->getData(), $productSuperAttributes);

        $productAbstractTransfer = (new ProductAbstractTransfer())
            ->setIdProductAbstract($productAbstractTransfer->getIdProductAbstract())
            ->setSku($productAbstractTransfer->getSku())
            ->setLocalizedAttributes($productAbstractTransfer->getLocalizedAttributes());

        $concreteProductCollection = $this->getFactory()
            ->getProductFacade()
            ->generateVariants($productAbstractTransfer, $attributeValues);

        if (!$concreteProductCollection) {
            $productConcreteTransfer = $this->copyProductAbstractToProductConcrete($productAbstractTransfer);

            return [$productConcreteTransfer];
        }

        return $concreteProductCollection;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductAbstractTransfer $productAbstractTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConcreteTransfer
     */
    protected function copyProductAbstractToProductConcrete(ProductAbstractTransfer $productAbstractTransfer): ProductConcreteTransfer
    {
        $productConcreteTransfer = (new ProductConcreteTransfer())
            ->setSku($productAbstractTransfer->getSku())
            ->setIsActive(false)
            ->setLocalizedAttributes($productAbstractTransfer->getLocalizedAttributes());
        foreach ($productAbstractTransfer->getPrices() as $price) {
            $productConcreteTransfer->addPrice(clone $price);
        }

        return $productConcreteTransfer;
    }
}
