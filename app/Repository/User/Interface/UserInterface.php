<?php

namespace App\Repository\User\Interface;

interface UserInterface
{
    public function index();

    public function create();

    public function store($data);

    public function edit($id);

    public function update($data, $id);

    public function destroy($id);

}
