<?php

namespace App\Repository\Role\Interface;

interface RoleInterface
{
    public function index();

    public function create();

    public function store($data);

    public function edit($id);

    public function update($data, $id);

    public function destroy($id);

}
