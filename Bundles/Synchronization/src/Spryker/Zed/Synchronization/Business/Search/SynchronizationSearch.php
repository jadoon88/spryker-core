<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Synchronization\Business\Search;

use Elastica\Exception\NotFoundException;
use Generated\Shared\Transfer\SearchDocumentTransfer;
use Spryker\Zed\Synchronization\Business\Synchronization\SynchronizationInterface;
use Spryker\Zed\Synchronization\Business\Validation\OutdatedValidatorInterface;
use Spryker\Zed\Synchronization\Dependency\Client\SynchronizationToSearchClientInterface;

class SynchronizationSearch implements SynchronizationInterface
{
    /**
     * @var string
     */
    public const KEY = 'key';

    /**
     * @var string
     */
    public const VALUE = 'value';

    /**
     * @var string
     */
    public const TYPE = 'type';

    /**
     * @var string
     */
    public const INDEX = 'index';

    /**
     * @var string
     */
    public const TIMESTAMP = '_timestamp';

    /**
     * @var \Spryker\Zed\Synchronization\Dependency\Client\SynchronizationToSearchClientInterface
     */
    protected $searchClient;

    /**
     * @var \Spryker\Zed\Synchronization\Business\Validation\OutdatedValidatorInterface
     */
    protected $outdatedValidator;

    /**
     * @param \Spryker\Zed\Synchronization\Dependency\Client\SynchronizationToSearchClientInterface $searchClient
     * @param \Spryker\Zed\Synchronization\Business\Validation\OutdatedValidatorInterface $outdatedValidator
     */
    public function __construct(
        SynchronizationToSearchClientInterface $searchClient,
        OutdatedValidatorInterface $outdatedValidator
    ) {
        $this->searchClient = $searchClient;
        $this->outdatedValidator = $outdatedValidator;
    }

    /**
     * @param array<string, mixed> $data
     * @param string $queueName
     *
     * @return void
     */
    public function write(array $data, $queueName)
    {
        $typeName = $this->getParam($data, static::TYPE);
        $indexName = $this->getParam($data, static::INDEX);

        $data = $this->formatTimestamp($data);
        $existingEntry = $this->read($data[static::KEY], $typeName);

        /** @var array<string, mixed> $formattedData */
        $formattedData = [
            $data[static::KEY] => $data[static::VALUE],
        ];

        if ($existingEntry !== null && $this->outdatedValidator->isInvalid($queueName, $data[static::VALUE], $existingEntry)) {
            return;
        }

        $this->searchClient->write($formattedData, $typeName, $indexName);
    }

    /**
     * @param array<string, mixed> $data
     * @param string $queueName
     *
     * @return void
     */
    public function delete(array $data, $queueName)
    {
        $typeName = $this->getParam($data, static::TYPE);
        $indexName = $this->getParam($data, static::INDEX);

        $data = $this->formatTimestamp($data);
        $existingEntry = $this->read($data[static::KEY], $typeName);

        /** @var array<string, mixed> $formattedData */
        $formattedData = [
            $data[static::KEY] => [],
        ];

        if ($existingEntry !== null && $this->outdatedValidator->isInvalid($queueName, $data[static::VALUE], $existingEntry)) {
            return;
        }

        $this->searchClient->delete($formattedData, $typeName, $indexName);
    }

    /**
     * @param array<string, mixed> $data
     * @param string $parameterName
     *
     * @return string
     */
    protected function getParam(array $data, $parameterName)
    {
        $value = '';
        if (isset($data['params'][$parameterName])) {
            $value = $data['params'][$parameterName];
        }

        return $value;
    }

    /**
     * @param string $key
     * @param string|null $typeName
     *
     * @return array|null
     */
    protected function read(string $key, ?string $typeName)
    {
        try {
            return $this->searchClient->read($key, $typeName)->getData();
        } catch (NotFoundException $exception) {
            return null;
        }
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array
     */
    protected function formatTimestamp(array $data)
    {
        if (!isset($data[static::VALUE][static::TIMESTAMP])) {
            return $data;
        }

        $data[static::VALUE]['timestamp'] = $data[static::VALUE][static::TIMESTAMP];
        unset($data[static::VALUE][static::TIMESTAMP]);

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return void
     */
    public function writeBulk(array $data): void
    {
        $dataSets = $this->prepareSearchDocumentTransfers($data);

        if ($dataSets === []) {
            return;
        }

        $this->searchClient->writeBulk($dataSets);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<\Generated\Shared\Transfer\SearchDocumentTransfer>
     */
    protected function prepareSearchDocumentTransfers(array $data): array
    {
        $searchDocumentTransfers = [];
        foreach ($data as $datum) {
            $typeName = $this->getParam($datum, static::TYPE);
            $indexName = $this->getParam($datum, static::INDEX);
            $key = $datum[static::KEY];
            $value = $datum[static::VALUE];
            unset($value['_timestamp']);

            $searchDocumentTransfer = new SearchDocumentTransfer();
            $searchDocumentTransfer->setType($typeName);
            $searchDocumentTransfer->setIndex($indexName);
            $searchDocumentTransfer->setId($key);
            $searchDocumentTransfer->setData($value);

            $searchDocumentTransfers[] = $searchDocumentTransfer;
        }

        return $searchDocumentTransfers;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return void
     */
    public function deleteBulk(array $data): void
    {
        $searchDocumentTransfers = $this->prepareSearchDocumentTransfers($data);

        if ($searchDocumentTransfers === []) {
            return;
        }

        $this->searchClient->deleteBulk($searchDocumentTransfers);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array
     */
    protected function prepareDeleteBulkDataSets(array $data): array
    {
        $dataSets = [];
        foreach ($data as $datum) {
            $key = $datum[static::KEY];
            $value = $datum[static::VALUE];

            unset($value['_timestamp']);
            $dataSets[$key] = $value;
        }

        return $dataSets;
    }
}
