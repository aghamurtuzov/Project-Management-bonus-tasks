<?php

namespace App\Http\Controllers;

use App\Repository\Role\Service\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $role;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct(RoleService $role)
    {
        $this->role = $role;
        $this->middleware('permission:role-list', ['only' => ['index']]);
        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:role-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = $this->role->index();

        return view('app.role.index', compact('roles'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = $this->role->create();
        return view('app.role.create',compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->role->store($request->all());

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->role->edit($id);

        return view('app.role.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $this->role->update($request->all(), $id);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->role->destroy($id);

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully');

    }
}
