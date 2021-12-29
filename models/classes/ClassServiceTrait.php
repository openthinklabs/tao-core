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
 * Copyright (c) 2018-2021 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\tao\model;

use Throwable;
use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\oatbox\service\ServiceManager;
use oat\tao\model\search\index\OntologyIndex;
use oat\tao\model\resources\Service\ClassDeleter;
use oat\generis\model\resource\Service\ResourceDeleter;
use oat\tao\model\resources\Contract\ClassDeleterInterface;
use oat\generis\model\resource\Contract\ResourceDeleterInterface;

trait ClassServiceTrait
{
    /**
     * Returns the root class of this service
     *
     * @return \core_kernel_classes_Class
     */
    abstract public function getRootClass();

    /**
     * @deprecated Use \oat\generis\model\resource\Service\ResourceDeleter::delete()
     *
     * @return bool
     */
    public function deleteResource(core_kernel_classes_Resource $resource)
    {
        try {
            $this->getResourceDeleter()->delete($resource);

            return true;
        } catch (Throwable $exception) {
            return false;
        }
    }

    /**
     * @deprecated Use \oat\tao\model\resources\Service\ClassDeleter::delete()
     *
     * @return bool
     */
    public function deleteClass(core_kernel_classes_Class $class)
    {
        try {
            $this->getClassDeleter()->delete($class);

            return true;
        } catch (Throwable $exception) {
            return false;
        }
    }

    /**
     * remove a class property
     *
     * @param \core_kernel_classes_Property $property
     * @return bool
     * @throws \common_exception_Error
     */
    public function deleteClassProperty(\core_kernel_classes_Property $property)
    {
        $indexes = $property->getPropertyValues(new \core_kernel_classes_Property(OntologyIndex::PROPERTY_INDEX));

        //delete property and the existing values of this property
        if ($returnValue = $property->delete(true)) {
            //delete index linked to the property
            foreach ($indexes as $indexUri) {
                $index = new core_kernel_classes_Resource($indexUri);
                $returnValue = $this->deletePropertyIndex($index);
            }
        }

        return $returnValue;
    }

    /**
     * remove an index property
     * @param \core_kernel_classes_Resource $index
     * @return bool
     */
    public function deletePropertyIndex(core_kernel_classes_Resource $index)
    {
        return $index->delete(true);
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return ServiceManager::getServiceManager();
    }

    private function getClassDeleter(): ClassDeleterInterface
    {
        return $this->getServiceManager()->getContainer()->get(ClassDeleter::class);
    }

    private function getResourceDeleter(): ResourceDeleterInterface
    {
        return $this->getServiceManager()->getContainer()->get(ResourceDeleter::class);
    }
}
