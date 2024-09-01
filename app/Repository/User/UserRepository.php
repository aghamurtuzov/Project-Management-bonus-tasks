<?php

namespace App\Repository\User;

use App\Models\User;
use App\Repository\User\Interface\UserInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRepository implements UserInterface
{
    protected $user;
    protected $role;

    public function __construct(User $user, Role $role)
    {
        $this->user = $user;
        $this->role = $role;
    }

    public function index()
    {
        $users = $this->user->orderByDesc('id')->get();
        return $users;
    }

    public function create()
    {
        $roles = $this->role->pluck('name', 'name')->all();
        return $roles;
    }

    public function store($data)
    {
        $user = $this->user::create($data);
        $user->assignRole($data['roles']);
        return $user;
    }

    public function edit($id)
    {
        $user = $this->user->findOrFail($id);
        $roles = $this->role->pluck('name', 'name')->all();
        $userRole = $user->roles ? $user->roles->pluck('name', 'name')->all() : [];

        return [
            'user' => $user,
            'roles' => $roles,
            'userRole' => $userRole,
        ];
    }

    public function update($data, $id)
    {
        if(!empty($data['password'])){
            $data['password'] = Hash::make($data['password']);
        }else{
            $data = Arr::except($data,array('password'));
        }

        $user = $this->user->find($id);
        $user->update($data);

        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($data['roles']);
        return $user;
    }

    public function destroy($id)
    {
        $user = $this->user->findOrFail($id);
        $user->delete();
        return $user;
    }

}
