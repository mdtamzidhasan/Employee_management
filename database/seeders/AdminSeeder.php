<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('Admin@1234'),
            'role'     => 'admin',
        ]);

        // Admin এরও একটা employee profile থাকবে
        Employee::create([
            'user_id'      => $admin->id,
            'phone'        => '+880 1700-000000',
            'department'   => 'Management',
            'position'     => 'System Administrator',
            'salary'       => 100000.00,
            'joining_date' => '2024-01-01',
            'address'      => 'Dhaka, Bangladesh',
            'status'       => 'active',
        ]);
    }
}