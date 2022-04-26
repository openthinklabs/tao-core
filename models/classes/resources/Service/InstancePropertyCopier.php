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

use core_kernel_classes_Resource;
use core_kernel_classes_Property;
use oat\tao\model\resources\Contract\InstancePropertyCopierInterface;

class InstancePropertyCopier implements InstancePropertyCopierInterface
{
    public function copy(
        core_kernel_classes_Resource $instance,
        core_kernel_classes_Property $property,
        core_kernel_classes_Resource $destinationInstance
    ): void {
        // TODO: Implement copy() method.
    }
}
