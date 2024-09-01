<?php
namespace App\Repository\User\Service;

use App\Repository\User\Interface\UserInterface;

class UserService {
    protected $user;

    public function __construct(UserInterface $user) {
        $this->user = $user;
    }

    public function index() {
        $users = $this->user->index();
        return $users;
    }

    public function create() {
        $roles = $this->user->create();
        return $roles;
    }

    public function store($data) {
        $user = $this->user->store($data);
        return $user;
    }

    public function edit($id) {
        $user = $this->user->edit($id);
        return $user;
    }

    public function update($data, $id) {
        $user = $this->user->update($data, $id);
        return $user;
    }

    public function destroy($id) {
        $user = $this->user->destroy($id);
        return $user;
    }

}
