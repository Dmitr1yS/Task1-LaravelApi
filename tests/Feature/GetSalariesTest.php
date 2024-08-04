<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Employee;
use App\Models\Transaction;

class GetSalariesTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_salaries_no_transactions()
    {
        $response = $this->getJson('/api/salaries');

        $response->assertStatus(200)
            ->assertJson([]);
    }

    public function test_get_salaries_with_transactions()
    {
        $employee1 = Employee::create([
            'email' => 'employee1@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $employee2 = Employee::create([
            'email' => 'employee2@example.com',
            'password' => bcrypt('secret123'),
        ]);

        Transaction::create([
            'employee_id' => $employee1->id,
            'hours' => 8,
            'paid' => false,
        ]);

        Transaction::create([
            'employee_id' => $employee1->id,
            'hours' => 4,
            'paid' => false,
        ]);

        Transaction::create([
            'employee_id' => $employee2->id,
            'hours' => 5,
            'paid' => false,
        ]);

        // Запрашиваем зарплаты
        $response = $this->getJson('/api/salaries');

        $response->assertStatus(200)
            ->assertJson([
                [
                    'employee_id' => $employee1->id,
                    'total' => '120.00',
                ],
                [
                    'employee_id' => $employee2->id,
                    'total' => '50.00',
                ],
            ]);
    }

    public function test_get_salaries_with_paid_transactions()
    {
        // Создаем сотрудников
        $employee = Employee::create([
            'email' => 'employee@example.com',
            'password' => bcrypt('secret123'),
        ]);

        // Создаем транзакции
        Transaction::create([
            'employee_id' => $employee->id,
            'hours' => 5,
            'paid' => false,
        ]);

        Transaction::create([
            'employee_id' => $employee->id,
            'hours' => 3,
            'paid' => true,
        ]);

        $response = $this->getJson('/api/salaries');

        $response->assertStatus(200)
            ->assertJson([
                [
                    'employee_id' => $employee->id,
                    'total' => 50.00,
                ],
            ]);
    }

    public function test_get_salaries_with_multiple_employees()
    {
        // Создаем сотрудников
        $employee1 = Employee::create(['email' => 'emp1@example.com', 'password' => bcrypt('password123')]);
        $employee2 = Employee::create(['email' => 'emp2@example.com', 'password' => bcrypt('password123')]);

        // Создаем транзакции
        Transaction::create(['employee_id' => $employee1->id, 'hours' => 2, 'paid' => false]);
        Transaction::create(['employee_id' => $employee1->id, 'hours' => 3, 'paid' => false]);
        Transaction::create(['employee_id' => $employee2->id, 'hours' => 4, 'paid' => false]);

        // Запрашиваем зарплаты
        $response = $this->getJson('/api/salaries');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'employee_id' => $employee1->id,
                'total' => '50.00',
            ])
            ->assertJsonFragment([
                'employee_id' => $employee2->id,
                'total' => '40.00',
            ]);
    }

    public function test_get_salaries_zero_if_no_transactions()
    {
        $employee = Employee::create(['email' => 'emp@example.com', 'password' => bcrypt('secret123')]);

        $response = $this->getJson('/api/salaries');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'employee_id' => $employee->id,
                'total' => '0.00',
            ]);
    }
}
