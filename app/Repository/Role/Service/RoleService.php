<?php
namespace App\Repository\Role\Service;


use App\Repository\Role\Interface\RoleInterface;

class RoleService {

    protected $role;

    public function __construct(RoleInterface $role) {
        $this->role = $role;
    }

    public function index() {
        $roles = $this->role->index();
        return $roles;
    }

    public function create() {
        $role = $this->role->create();
        return $role;
    }

    public function store($data) {
        $role = $this->role->store($data);
        return $role;
    }

    public function edit($id) {
        $role = $this->role->edit($id);
        return $role;
    }

    public function update($data, $id) {
        $role = $this->role->update($data, $id);
        return $role;
    }

    public function destroy($id) {
        $role = $this->role->destroy($id);
        return $role;
    }

}
