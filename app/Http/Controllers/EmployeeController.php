<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required|email|unique:employees',
            'password' => 'required|min:6',
        ]);

        $employee = Employee::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json($employee, 201);
    }
}
