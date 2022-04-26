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
use oat\tao\model\resources\Contract\InstancePropertyCopierInterface;

class InstanceCopier
{
    /** @var InstancePropertyCopierInterface */
    private $instancePropertyCopier;

    public function __construct(InstancePropertyCopierInterface $instancePropertyCopier)
    {
        $this->instancePropertyCopier = $instancePropertyCopier;
    }

    public function copy(
        core_kernel_classes_Resource $instance,
        core_kernel_classes_Class $destinationClass
    ): core_kernel_classes_Resource {
        $newInstance = $destinationClass->createInstance($instance->getLabel());

        if ($newInstance === null) {
            throw new RuntimeException(
                sprintf(
                    'New instance was not created. Original instance uri: %s',
                    $instance->getUri()
                )
            );
        }

        foreach ($destinationClass->getProperties(true) as $property) {
            $this->instancePropertyCopier->copy($instance, $property, $newInstance);
        }

        return $newInstance;
    }
}
