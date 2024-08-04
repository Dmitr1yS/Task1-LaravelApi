<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Transaction;
use App\Models\Employee;

class PaySalariesTest extends TestCase
{
    use RefreshDatabase;

    public function test_pay_salaries_success()
    {
        $employee = Employee::create([
            'email' => 'employee@example.com',
            'password' => bcrypt('secret123'),
        ]);

        Transaction::create(['employee_id' => $employee->id, 'hours' => 10, 'paid' => false]);
        Transaction::create(['employee_id' => $employee->id, 'hours' => 5, 'paid' => false]);

        $response = $this->postJson('/api/pay-salaries');

        $response->assertStatus(200)
            ->assertJson(['message' => 'All unpaid transactions have been marked as paid.']);

        $this->assertDatabaseHas('transactions', [
            'employee_id' => $employee->id,
            'hours' => 10,
            'paid' => true,
        ]);

        $this->assertDatabaseHas('transactions', [
            'employee_id' => $employee->id,
            'hours' => 5,
            'paid' => true,
        ]);
    }

    public function test_pay_salaries_no_unpaid_transactions()
    {
        $employee = Employee::create([
            'email' => 'employee@example.com',
            'password' => bcrypt('secret123'),
        ]);

        Transaction::create(['employee_id' => $employee->id, 'hours' => 10, 'paid' => true]);

        $response = $this->postJson('/api/pay-salaries');

        $response->assertStatus(200)
            ->assertJson(['message' => 'All unpaid transactions have been marked as paid.']);

        $this->assertDatabaseHas('transactions', [
            'employee_id' => $employee->id,
            'hours' => 10,
            'paid' => true,
        ]);
    }
}
