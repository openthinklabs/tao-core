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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\tao\model\accessControl\Service;

use core_kernel_classes_Resource;
use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyRdfs;
use oat\tao\model\exceptions\UserErrorException;
use tao_models_classes_RoleService;

class DeleteRoleService
{
    /** @var tao_models_classes_RoleService */
    private $roleService;

    /** @var InternalRoleSpecification */
    private $internalRoleSpecification;

    /** @var string[] */
    private $forbiddenRoles = [];

    public function __construct(
        InternalRoleSpecification $internalRoleSpecification,
        tao_models_classes_RoleService $roleService
    ) {
        $this->internalRoleSpecification = $internalRoleSpecification;
        $this->roleService = $roleService;
    }

    public function withForbiddenRoles(array $forbiddenRoles): self
    {
        $this->forbiddenRoles = $forbiddenRoles;

        return $this;
    }

    public function delete(core_kernel_classes_Resource $role): void
    {
        $isWritable = $role->isWritable();

        if ($isWritable && $this->internalRoleSpecification->isSatisfiedBy($role)) {
            throw new UserErrorException(__('Unable to delete the selected resource'));
        }

        if (!$isWritable && $this->deleteDuplicatedFields($role)) {
            return;
        }

        if (!$isWritable || $this->isForbidden($role)) {
            throw new UserErrorException(__('Unable to delete the selected resource'));
        }

        $users = $role->getClass(GenerisRdf::CLASS_GENERIS_USER)->searchInstances(
            [
                GenerisRdf::PROPERTY_USER_ROLES => $role->getUri()
            ],
            [
                'recursive' => true,
                'like' => false
            ]
        );

        if (!empty($users)) {
            throw new UserErrorException(
                __('This role is still given to one or more users. Please remove the role to these users first.')
            );
        }

        if (!$this->roleService->removeRole($role)) {
            throw new UserErrorException(__('Unable to delete the selected resource'));
        }
    }

    private function deleteDuplicatedFields(core_kernel_classes_Resource $role): bool
    {
        return $role->removePropertyValues($role->getProperty(OntologyRdfs::RDFS_LABEL));
    }

    private function isForbidden(core_kernel_classes_Resource $role): bool
    {
        return in_array($role->getUri(), $this->forbiddenRoles, true);
    }
}
