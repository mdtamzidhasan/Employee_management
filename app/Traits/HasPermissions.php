<?php

namespace App\Traits;

use App\Services\RbacApiService;

trait HasPermissions
{
    protected ?array $_permissions = null;
    protected ?array $_groupedPermissions = null;

    protected function loadPermissions(): void
    {
        if ($this->_permissions !== null) return;

        $user = auth()->user();

        if (!$user) {
            $this->_permissions        = [];
            $this->_groupedPermissions = [];
            return;
        }

        if ($user->isAdmin()) {
            $this->_permissions        = ['*']; // Admin সব পাবে
            $this->_groupedPermissions = [];
            return;
        }

        $rbac = app(RbacApiService::class);
        $this->_permissions        = $rbac->getUserPermissions($user->id);
        $this->_groupedPermissions = $rbac->getGroupedPermissions($user->id);
    }

    protected function can(string $permission): bool
    {
        $this->loadPermissions();
        if (in_array('*', $this->_permissions)) return true;
        return in_array($permission, $this->_permissions);
    }

    protected function canAny(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->can($permission)) return true;
        }
        return false;
    }

    protected function getGrouped(): array
    {
        $this->loadPermissions();
        return $this->_groupedPermissions ?? [];
    }
}