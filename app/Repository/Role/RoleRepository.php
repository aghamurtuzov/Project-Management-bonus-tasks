<?php

namespace App\Repository\Role;

use App\Repository\Role\Interface\RoleInterface;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleRepository implements RoleInterface
{
    protected $role;
    protected $permission;

    public function __construct(Role $role, Permission $permission)
    {
        $this->role = $role;
        $this->permission = $permission;
    }

    public function index()
    {
        $roles = $this->role->orderByDesc('id')->get();
        return $roles;
    }

    public function create()
    {
        $permission = $this->permission::all();
        return $permission;
    }

    public function store($data)
    {
        $permissionsID = array_map(
            function ($value) {
                return (int)$value;
            },
            $data['permission']
        );

        $role = $this->role->create(['name' => $data['name']]);
        $role->syncPermissions($permissionsID);

        return $role;
    }

    public function edit($id)
    {
        $role = $this->role->find($id);
        $permission = $this->permission->get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return [
            "role" => $role,
            "permission" => $permission,
            "rolePermissions" => $rolePermissions
        ];
    }

    public function update($data, $id)
    {
        $role = $this->role->findOrFail($id);
        $role->fill($data);
        $role->save();

        $permissionsID = array_map(
            function ($value) {
                return (int)$value;
            },
            $data['permission']
        );

        $role->syncPermissions($permissionsID);

        return $role;
    }

    public function destroy($id)
    {
        $role = $this->role->findOrFail($id);
        $role->delete();
        return $role;
    }

}
