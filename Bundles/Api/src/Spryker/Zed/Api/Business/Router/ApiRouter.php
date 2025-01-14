<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Api\Business\Router;

use Spryker\Zed\Api\ApiConfig;
use Spryker\Zed\Api\Communication\Controller\AbstractApiController;
use Spryker\Zed\Kernel\ClassResolver\Controller\ControllerResolver;
use Spryker\Zed\Kernel\Communication\BundleControllerAction;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use TypeError;

class ApiRouter implements RouterInterface
{
    /**
     * @var string
     */
    protected const MODULE_NAME = 'Api';

    /**
     * @var string
     */
    protected const CONTROLLER_NAME = 'Rest';

    /**
     * @var string
     */
    protected const ACTION_NAME = 'index';

    /**
     * @var \Spryker\Zed\Api\ApiConfig
     */
    protected $config;

    /**
     * @var \Symfony\Component\Routing\RequestContext
     */
    protected $context;

    /**
     * @param \Spryker\Zed\Api\ApiConfig $config
     */
    public function __construct(ApiConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param \Symfony\Component\Routing\RequestContext $context
     *
     * @return void
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    /**
     * @return \Symfony\Component\Routing\RequestContext
     */
    public function getContext(): RequestContext
    {
        return $this->context;
    }

    /**
     * Gets the RouteCollection instance associated with this Router.
     *
     * @return \Symfony\Component\Routing\RouteCollection A RouteCollection instance
     */
    public function getRouteCollection()
    {
        return new RouteCollection();
    }

    /**
     * {@inheritDoc}
     *
     * @param string $name
     * @param array $parameters
     * @param int $referenceType
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     *
     * @return string The generated URL
     */
    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        throw new RouteNotFoundException();
    }

    /**
     * {@inheritDoc}
     *
     * @param string $pathinfo
     *
     * @throws \TypeError
     *
     * @return array
     */
    public function match(string $pathinfo): array
    {
        if (!$this->config->isApiEnabled()) {
            return [];
        }

        $this->assertValidPath($pathinfo);

        $controllerResolver = new ControllerResolver();
        $bundleControllerAction = new BundleControllerAction(
            static::MODULE_NAME,
            static::CONTROLLER_NAME,
            static::ACTION_NAME,
        );

        /** @var \Spryker\Zed\Kernel\Communication\Controller\AbstractController $controller */
        $controller = $controllerResolver->resolve($bundleControllerAction);

        if (!$controller instanceof AbstractApiController) {
            $class = get_class($controller);

            throw new TypeError(sprintf('"%s" should be an instance of "%s"', $class, AbstractApiController::class));
        }
        $controller->initialize();

        return [
            '_controller' => [$controller, static::ACTION_NAME . 'Action'],
            '_route' => $this->getRoute(),
        ];
    }

    /**
     * @param string $path
     *
     * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException
     *
     * @return void
     */
    protected function assertValidPath(string $path): void
    {
        if (strpos($path, ApiConfig::ROUTE_PREFIX_API_REST) !== 0) {
            throw new ResourceNotFoundException(sprintf(
                'Invalid URI prefix, expected "%s" in path "%s"',
                ApiConfig::ROUTE_PREFIX_API_REST,
                $path,
            ));
        }
    }

    /**
     * @return string
     */
    protected function getRoute(): string
    {
        return sprintf('%s/%s/%s', static::MODULE_NAME, static::CONTROLLER_NAME, static::ACTION_NAME);
    }
}
