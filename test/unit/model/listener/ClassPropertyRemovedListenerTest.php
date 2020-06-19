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

use oat\generis\test\MockObject;
use oat\generis\test\TestCase;
use oat\oatbox\service\ServiceManager;
use oat\tao\model\event\ClassPropertyRemovedEvent;
use oat\tao\model\listener\ClassPropertyRemovedListener;
use oat\tao\model\search\tasks\DeleteIndexProperty;
use oat\tao\model\taskQueue\QueueDispatcherInterface;

class ClassPropertyRemovedListenerTest extends TestCase
{
    /** @var QueueDispatcherInterface|MockObject */
    private $queueDispatcher;

    /** @var ClassPropertyRemovedListener */
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new ClassPropertyRemovedListener();

        $serviceManager = $this->createMock(ServiceManager::class);

        $this->sut->setServiceLocator($serviceManager);

        $this->queueDispatcher = $this->createMock(QueueDispatcherInterface::class);

        $serviceManager->expects($this->any())
            ->method('get')
            ->with(QueueDispatcherInterface::SERVICE_ID)
            ->willReturn($this->queueDispatcher);

        ServiceManager::setServiceManager($serviceManager);
    }

    public function testRemoveClassProperty(): void
    {
        $class = $this->createMock(core_kernel_classes_Class::class);
        $property = $this->createMock(core_kernel_classes_Property::class);

        $this->queueDispatcher->expects($this->once())
            ->method('createTask')
            ->with(
                new DeleteIndexProperty(),
                [
                    $class,
                    $property
                ],
                'Updating search index',
                null,
                false
            );

        $this->sut->removeClassProperty(new ClassPropertyRemovedEvent($class, $property));
    }
}
