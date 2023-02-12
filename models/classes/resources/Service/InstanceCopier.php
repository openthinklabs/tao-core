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
 *
 * @author Andrei Shapiro <andrei.shapiro@taotesting.com>
 */

declare(strict_types=1);

namespace oat\tao\model\resources\Service;

use RuntimeException;
use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\tao\model\resources\Contract\InstanceCopierInterface;
use oat\tao\model\resources\Contract\InstanceContentCopierInterface;
use oat\tao\model\resources\Contract\InstanceMetadataCopierInterface;

class InstanceCopier implements InstanceCopierInterface
{
    /** @var InstanceMetadataCopierInterface */
    private $instanceMetadataCopier;

    /** @var InstanceContentCopierInterface */
    private $instanceContentCopier;

    public function __construct(InstanceMetadataCopierInterface $instanceMetadataCopier)
    {
        $this->instanceMetadataCopier = $instanceMetadataCopier;
    }

    public function withInstanceContentCopier(InstanceContentCopierInterface $instanceContentCopier): void
    {
        $this->instanceContentCopier = $instanceContentCopier;
    }

    /**
     * @inheritDoc
     */
    public function copy(
        core_kernel_classes_Resource $instance,
        core_kernel_classes_Class $destinationClass
    ): core_kernel_classes_Resource {
        $newInstance = $destinationClass->createInstance($instance->getLabel());

        if ($newInstance === null) {
            throw new RuntimeException(
                sprintf(
                    'New instance was not created. Original instance uri: %s, destination class uri: %s',
                    $instance->getUri(),
                    $destinationClass->getUri()
                )
            );
        }

        $this->instanceMetadataCopier->copy($instance, $newInstance);

        if (isset($this->instanceContentCopier)) {
            $this->instanceContentCopier->copy($instance, $newInstance);
        }

        return $newInstance;
    }
}
