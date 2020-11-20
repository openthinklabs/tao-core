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
 */

declare(strict_types=1);

namespace oat\tao\test\unit\model\search;

use core_kernel_classes_Resource;
use oat\generis\model\data\Ontology;
use oat\tao\model\search\SearchQuery;
use PHPUnit\Framework\MockObject\MockObject;
use oat\generis\model\data\permission\PermissionHelper;
use oat\generis\test\TestCase;
use oat\tao\model\search\ResultSet;
use oat\tao\model\search\ResultSetResponseNormalizer;

class ResultSetResponseNormalizerTest extends TestCase
{
    /** @var ResultSetResponseNormalizer */
    private $subject;

    /** @var PermissionHelper|MockObject */
    private $permissionHelperMock;

    /** @var SearchQuery|MockObject */
    private $searchQueryMock;

    /** @var ResultSet|MockObject */
    private $resultSetMock;

    /** @var Ontology|MockObject */
    private $modelMock;

    /** @var core_kernel_classes_Resource|MockObject */
    private $resourceMock;

    public function setUp(): void
    {
        $this->permissionHelperMock = $this->createMock(PermissionHelper::class);
        $this->searchQueryMock = $this->createMock(SearchQuery::class);
        $this->resultSetMock = $this->createMock(ResultSet::class);
        $this->modelMock = $this->createMock(Ontology::class);
        $this->resourceMock = $this->createMock(core_kernel_classes_Resource::class);

        $this->resourceMock
            ->method('getUri')
            ->willReturn('uri1');

        $this->resourceMock
            ->method('getLabel')
            ->willReturn('label');

        $this->modelMock
            ->method('getResource')
            ->willReturn($this->resourceMock);

        $this->resultSetMock
            ->method('getArrayCopy')
            ->willReturn(
                [
                    'uri1',
                    'uri2',
                ]
            );

        $this->permissionHelperMock
            ->expects($this->once())
            ->method('filterByPermission')
            ->willReturn(
                [
                    'uri1',
                ]
            );

        $this->subject = new ResultSetResponseNormalizer();
        $this->subject->setServiceLocator(
            $this->getServiceLocatorMock(
                [
                    PermissionHelper::class => $this->permissionHelperMock
                ]
            )
        );

        $this->subject->setModel($this->modelMock);
    }

    public function testNormalize()
    {
        $this->resultSetMock
            ->expects($this->exactly(2))
            ->method('getTotalCount')
            ->willReturn(100);

        $this->searchQueryMock
            ->method('getRows')
            ->willReturn(2);

        $this->searchQueryMock
            ->method('getPage')
            ->willReturn(1);

        $result = $this->subject->normalize($this->searchQueryMock, $this->resultSetMock);
        $this->assertResult($result);
    }

    public function testNormalizeWithEmptyRows()
    {
        $this->resultSetMock
            ->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(100);

        $this->searchQueryMock
            ->method('getRows')
            ->willReturn(0);

        $this->permissionHelperMock
            ->expects($this->once())
            ->method('filterByPermission')
            ->willReturn(
                [
                    'uri1',
                ]
            );

        $this->searchQueryMock
            ->method('getPage')
            ->willReturn(1);

        $result = $this->subject->normalize($this->searchQueryMock, $this->resultSetMock);
        $this->assertResult($result);
    }


    private function assertResult(array $result): void
    {
        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('readonly', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('page', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('totalCount', $result);
        $this->assertArrayHasKey('records', $result);
        $this->assertEquals(1, $result['page']);
        $this->assertEquals(100, $result['totalCount']);
        $this->assertEquals(2, $result['records']);
        $this->assertCount(2, $result['data']);
        $this->assertCount(1, $result['readonly']);
        $this->assertEquals(
            [
                [
                    'id' => 'uri1',
                    'http://www.w3.org/2000/01/rdf-schema#label' => 'label',
                ],
                [
                    'id' => 'uri1',
                    'http://www.w3.org/2000/01/rdf-schema#label' => 'label',
                ],
            ],
            $result['data']
        );
        $this->assertTrue($result['readonly']['uri2']);
    }
}
