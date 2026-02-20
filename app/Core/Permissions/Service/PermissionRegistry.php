<?php

namespace App\Core\Permissions\Service;

use App\Core\Permissions\Models\Permission;

class PermissionRegistry
{

    protected array $permissions = [];

    public function registerMany(string $module, array $permissions): void
    {
        foreach ($permissions as $name => $permission) {
            $this->register($module, $name, $permission['name'], $permission['description']);
        }
    }

    public function register(string $module, string $permission, string $name, string $description): void
    {
        $this->permissions[$permission] = [
            'module' => $module,
            'name' => $permission,
            'description' => $description,
            'display_name' => $name
        ];
    }

    public function groupByModule(): array
    {
        $permissions = [];

        foreach ($this->all() as $permission) {
            $permissions[$permission['module']][] = $permission;
        }

        return $permissions;
    }

    public function all(): array
    {
        return $this->permissions;
    }

    public function sync(): void
    {
        $permissions = collect($this->permissions)->select('name', 'module')->values()->all();
        Permission::upsert($permissions, ['name']);
    }
}
