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
 * Copyright (c) 2022-2023 (original work) Open Assessment Technologies SA.
 *
 * @author Andrei Shapiro <andrei.shapiro@taotesting.com>
 */

declare(strict_types=1);

namespace oat\tao\model\resources\Contract;

use RuntimeException;
use core_kernel_classes_Class;
use core_kernel_classes_Resource;

/**
 * @deprecated Please use new interface oat\tao\model\resources\Contract\ResourceTransferInterface
 */
interface InstanceCopierInterface
{
    /**
     * @throws RuntimeException
     * @deprecated Please use new interface oat\tao\model\resources\Contract\ResourceTransferInterface
     */
    public function copy(
        core_kernel_classes_Resource $instance,
        core_kernel_classes_Class $destinationClass
    ): core_kernel_classes_Resource;
}
