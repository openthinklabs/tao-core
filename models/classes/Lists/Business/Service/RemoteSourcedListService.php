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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 *
 */

declare(strict_types=1);

namespace oat\tao\model\Lists\Business\Service;

use core_kernel_classes_Property as RdfProperty;
use core_kernel_persistence_Exception;
use oat\tao\model\Lists\Business\Domain\ValueCollection;
use oat\tao\model\service\InjectionAwareService;
use oat\taoBackOffice\model\lists\ListService;
use RuntimeException;

class RemoteSourcedListService extends InjectionAwareService
{
    public const SERVICE_ID = 'tao/RemoteSourcedListService';

    public const PROPERTY_SOURCE_URI = 'http://www.tao.lu/Ontologies/TAO.rdf#RemoteListSource';
    public const PROPERTY_ITEM_URI_PATH = 'http://www.tao.lu/Ontologies/TAO.rdf#RemoteListItemUriPath';
    public const PROPERTY_ITEM_LABEL_PATH = 'http://www.tao.lu/Ontologies/TAO.rdf#RemoteListItemLabelPath';

    /** @var ValueCollectionService */
    private $valueCollectionService;
    /** @var RemoteSource */
    private $remoteSource;

    /**
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     *
     * @param ValueCollectionService $valueCollectionService
     * @param RemoteSource           $remoteSource
     */
    public function __construct(
        ValueCollectionService $valueCollectionService,
        RemoteSource $remoteSource
    ) {
        $this->valueCollectionService = $valueCollectionService;
        $this->remoteSource = $remoteSource;
    }

    public function createList(string $label, string $source, string $labelPath, string $uriPath): string
    {
        $class = $this->getListService()->createList($label);

        $propertyType = new RdfProperty('http://www.tao.lu/Ontologies/TAO.rdf#ListType');
        $propertyRemote = new RdfProperty('http://www.tao.lu/Ontologies/TAO.rdf#ListRemote');
        $class->setPropertyValue($propertyType, $propertyRemote);

        $propertySource = new RdfProperty(self::PROPERTY_SOURCE_URI);
        $class->setPropertyValue($propertySource, $source);

        $propertySource = new RdfProperty(self::PROPERTY_ITEM_LABEL_PATH);
        $class->setPropertyValue($propertySource, $labelPath);

        $propertySource = new RdfProperty(self::PROPERTY_ITEM_URI_PATH);
        $class->setPropertyValue($propertySource, $uriPath);

        return $class->getUri();
    }

    /**
     * @param string $listUri
     *
     * @throws core_kernel_persistence_Exception
     */
    public function sync(string $listUri): void
    {
        $listService = $this->getListService();
        $listClass = $listService->getList($listUri);

        if ($listClass === null) {
            throw new RuntimeException(sprintf('Wrong remote list uri %s', $listUri));
        }

        if (!$listService->isRemote($listClass)) {
            throw new RuntimeException(sprintf('List %s is not remote', $listUri));
        }

        $sourceUrl = (string)$listClass->getOnePropertyValue($listClass->getProperty(self::PROPERTY_SOURCE_URI));
        $uriPath = (string)$listClass->getOnePropertyValue($listClass->getProperty(self::PROPERTY_ITEM_URI_PATH));
        $labelPath = (string)$listClass->getOnePropertyValue($listClass->getProperty(self::PROPERTY_ITEM_LABEL_PATH));

        $collection = new ValueCollection(
            $listUri,
            ...iterator_to_array($this->remoteSource->fetch($sourceUrl, $uriPath, $labelPath, 'jsonpath'))
        );

        $result = $this->valueCollectionService->persist($collection);

        if (!$result) {
            throw new RuntimeException('Sync was not successful');
        }
    }

    protected function getListService(): ListService
    {
        return ListService::singleton();
    }
}
