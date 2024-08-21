<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Roles;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'isAdmin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $roles = Roles::all();
        return response()->json($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|in:user,admin,owner',
        ]);

        $role = Roles::create($request->all());

        return response()->json([
            'message' => 'Role berhasil dibuat',
            'role' => $role
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Roles::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|in:user,admin,owner',
        ]);

        $role->update($request->all());

        return response()->json([
            'message' => 'Role berhasil diupdate',
            'role' => $role
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Roles::findOrFail($id);
        $role->delete();

        return response()->json([
            'message' => 'Role berhasil dihapus'
        ]);
    }
}
