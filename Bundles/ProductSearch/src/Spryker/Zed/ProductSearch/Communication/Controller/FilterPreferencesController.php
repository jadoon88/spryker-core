<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductSearch\Communication\Controller;

use Generated\Shared\Transfer\ProductSearchAttributeTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\ProductSearch\Communication\ProductSearchCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductSearch\Business\ProductSearchFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductSearch\Persistence\ProductSearchQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\ProductSearch\Persistence\ProductSearchRepositoryInterface getRepository()
 */
class FilterPreferencesController extends AbstractController
{
    /**
     * @var string
     */
    public const PARAM_ID = 'id';

    /**
     * @var string
     */
    public const PARAM_TERM = 'term';

    /**
     * @var string
     */
    public const MESSAGE_FILTER_PREFERENCE_CREATE_SUCCESS = 'Filter preference was created successfully.';

    /**
     * @var string
     */
    public const MESSAGE_FILTER_PREFERENCE_UPDATE_SUCCESS = 'Filter preference was updated successfully.';

    /**
     * @var string
     */
    public const REDIRECT_URL_DEFAULT = '/product-search/filter-preferences';

    /**
     * @return array
     */
    public function indexAction()
    {
        $filterPreferencesTable = $this->getFactory()->createFilterPreferencesTable();

        return $this->viewResponse([
            'filterPreferencesTable' => $filterPreferencesTable->render(),
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction()
    {
        $table = $this->getFactory()->createFilterPreferencesTable();

        return $this->jsonResponse(
            $table->fetchData(),
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function createAction(Request $request)
    {
        $dataProvider = $this
            ->getFactory()
            ->createFilterPreferencesDataProvider();

        $form = $this->getFactory()
            ->createFilterPreferencesForm(
                $dataProvider->getData(),
                $dataProvider->getOptions(),
            )
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productSearchAttributeTransfer = $this
                ->getFactory()
                ->createAttributeFormTransferMapper()
                ->createTransfer($form);

            $productSearchAttributeTransfer = $this->getFacade()->createProductSearchAttribute($productSearchAttributeTransfer);

            $this->addSuccessMessage(static::MESSAGE_FILTER_PREFERENCE_CREATE_SUCCESS);

            return $this->redirectResponse(sprintf(
                '/product-search/filter-preferences/view?%s=%d',
                static::PARAM_ID,
                $productSearchAttributeTransfer->getIdProductSearchAttribute(),
            ));
        }

        return $this->viewResponse([
            'form' => $form->createView(),
            'currentLocale' => $this->getFactory()->getLocaleFacade()->getCurrentLocale()->getLocaleName(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function editAction(Request $request)
    {
        $idProductSearchAttribute = $this->castId($request->query->getInt(static::PARAM_ID));

        $dataProvider = $this
            ->getFactory()
            ->createFilterPreferencesDataProvider();

        $filterPreferencesFormData = $dataProvider->getData($idProductSearchAttribute);

        if ($filterPreferencesFormData === []) {
            $this->addErrorMessage("Attribute with id %s doesn't exist", ['%s' => $idProductSearchAttribute]);

            return $this->redirectResponse(static::REDIRECT_URL_DEFAULT);
        }

        $form = $this->getFactory()
            ->createFilterPreferencesForm(
                $filterPreferencesFormData,
                $dataProvider->getOptions($idProductSearchAttribute),
            )
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productSearchAttributeTransfer = $this
                ->getFactory()
                ->createAttributeFormTransferMapper()
                ->createTransfer($form);

            $productSearchAttributeTransfer = $this->getFacade()->updateProductSearchAttribute($productSearchAttributeTransfer);

            $this->addSuccessMessage(static::MESSAGE_FILTER_PREFERENCE_UPDATE_SUCCESS);

            return $this->redirectResponse(sprintf(
                '/product-search/filter-preferences/view?%s=%d',
                static::PARAM_ID,
                $productSearchAttributeTransfer->getIdProductSearchAttribute(),
            ));
        }

        return $this->viewResponse([
            'form' => $form->createView(),
            'currentLocale' => $this->getFactory()->getLocaleFacade()->getCurrentLocale()->getLocaleName(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function viewAction(Request $request)
    {
        $idProductSearchAttribute = $this->castId($request->query->getInt(static::PARAM_ID));

        $attributeTransfer = $this->getFacade()->getProductSearchAttribute($idProductSearchAttribute);

        if (!$attributeTransfer) {
            return $this->redirectResponse('/product-search/filter-preferences');
        }

        $deleteForm = $this->getFactory()->createDeleteFilterPreferencesForm();

        return $this->viewResponse([
            'attributeTransfer' => $attributeTransfer,
            'locales' => $this->getFactory()->getLocaleFacade()->getLocaleCollection(),
            'deleteForm' => $deleteForm->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function deleteAction(Request $request)
    {
        $form = $this->getFactory()->createDeleteFilterPreferencesForm()->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addErrorMessage('CSRF token is not valid.');

            return $this->redirectResponse(static::REDIRECT_URL_DEFAULT);
        }

        $idProductSearchAttribute = $this->castId($request->query->getInt(static::PARAM_ID));

        $productSearchAttributeTransfer = new ProductSearchAttributeTransfer();
        $productSearchAttributeTransfer->setIdProductSearchAttribute($idProductSearchAttribute);

        $this->getFacade()->deleteProductSearchAttribute($productSearchAttributeTransfer);

        $this->addSuccessMessage('Filter successfully deleted.');

        return $this->redirectResponse(static::REDIRECT_URL_DEFAULT);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function keysAction(Request $request)
    {
        $searchTerm = (string)$request->query->get(static::PARAM_TERM);

        $keys = $this->getFacade()->suggestUnusedProductSearchAttributeKeys($searchTerm);

        return $this->jsonResponse($keys);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function syncAction()
    {
        $this->getFacade()->touchProductAbstractByAsynchronousAttributes();
        $this->getFacade()->touchProductSearchConfigExtension();

        $this->addSuccessMessage('Filter preferences synchronization was successful.');

        return $this->redirectResponse('/product-search/filter-preferences');
    }
}
