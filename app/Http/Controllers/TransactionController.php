<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Employee;

class TransactionController extends Controller
{
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'hours' => 'required|numeric|min:0',
        ]);

        $transaction = Transaction::create([
            'employee_id' => $request->employee_id,
            'hours' => $request->hours,
        ]);

        return response()->json($transaction, 201);
    }

    public function getSalaries()
    {
        $employees = Employee::all();

        $salaries = Transaction::where('paid', false)
            ->selectRaw('employee_id, SUM(hours * 10) as total')
            ->groupBy('employee_id')
            ->get()
            ->keyBy('employee_id');

        $result = $employees->map(function($employee) use ($salaries) {
            return [
                'employee_id' => $employee->id,
                'total' => isset($salaries[$employee->id]) ? $salaries[$employee->id]->total : '0.00'
            ];
        });

        return response()->json($result);
    }

    public function paySalaries(): \Illuminate\Http\JsonResponse
    {
        Transaction::where('paid', false)->update(['paid' => true]);

        return response()->json(['message' => 'All unpaid transactions have been marked as paid.']);
    }
}
