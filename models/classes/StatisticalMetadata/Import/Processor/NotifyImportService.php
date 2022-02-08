<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\tao\model\StatisticalMetadata\Import\Processor;

use core_kernel_classes_Resource;
use Google\Cloud\PubSub\PubSubClient;
use oat\tao\model\metadata\compiler\ResourceMetadataCompilerInterface;
use oat\tao\model\StatisticalMetadata\DataStore\Compiler\StatisticalJsonResourceMetadataCompiler;
use Psr\Log\LoggerInterface;
use Throwable;

class NotifyImportService
{
    private const DEFAULT_MAX_TRIES = 10;

    /** @var LoggerInterface */
    private $logger;

    /** @var ResourceMetadataCompilerInterface */
    private $resourceMetadataCompiler;

    /** @var int */
    private $maxTries = self::DEFAULT_MAX_TRIES;

    /** @var core_kernel_classes_Resource[] */
    private $resources = [];

    /** @var array */
    private $aliases = [];

    public function __construct(LoggerInterface $logger, ResourceMetadataCompilerInterface $jsonMetadataCompiler)
    {
        $this->logger = $logger;
        $this->resourceMetadataCompiler = $jsonMetadataCompiler;
    }

    public function withMaxTries(int $maxTries): self
    {
        $this->maxTries = $maxTries;

        return $this;
    }

    public function addResource(core_kernel_classes_Resource $resource): self
    {
        $this->resources[] = $resource;

        return $this;
    }

    public function withAliases(array $aliases): self
    {
        $this->aliases = $aliases;

        return $this;
    }

    public function notify(): void
    {
        // @TODO Migrate pub/sub to proper abstraction
        // @TODO Add proper pub/sub credentials via config

        $data = [];
        $resourceIds = [];

        if ($this->resourceMetadataCompiler instanceof StatisticalJsonResourceMetadataCompiler) {
            $this->resourceMetadataCompiler->withAliases($this->aliases);
        }

        foreach ($this->resources as $resource) {
            $resourceIds[] = $resource->getUri();
            $data[] = $this->resourceMetadataCompiler->compile($resource);
        }

        $topicId = 'oat-demo-delivery-processing-topic';
        $tries = 0;

        do {
            try {
                $tries++;

                $topic = $this->getPubSub()->topic($topicId);

                $messageIds = $topic->publish(
                    [
                        'data' => json_encode($data),
                    ]
                );

                $this->logger->info(
                    sprintf(
                        'Pub/Sub messages "%s" send for Statistical data for resources "%s"',
                        var_export($messageIds, true),
                        implode(', ', $resourceIds)
                    )
                );

                return;
            } catch (Throwable $exception) {
                $this->logger->error(
                    sprintf(
                        'Error while Pub/Sub messages for Statistical data: "%s" - resources: "%s"',
                        $exception->getMessage(),
                        implode(', ', $resourceIds)
                    )
                );
            }
        } while ($tries < $this->maxTries);

        throw new \Exception('Sync error', 0, $exception); //@TODO Provide proper exception
    }

    private function getPubSub(): PubSubClient
    {
        $keyFilePath = __DIR__ . '/../../../../../../oat-dev-eu-key.json';

        return new PubSubClient(
            [
                'keyFilePath' => $keyFilePath,
            ]
        );
    }
}
