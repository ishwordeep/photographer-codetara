<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(LiveSeeder::class);

        User::factory()->create([
            'name' => 'Super User',
            'email' => 'super@admin.com',
            'password' => bcrypt('superadmin'),
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);

        DB::table('users')->insert([
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'), // Optional: if you want to set a default password
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'admin',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'), // Optional
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'admin',
            ],
            [
                'name' => 'James Brown',
                'email' => 'ja@jjaa.com',
                'password' => Hash::make('password'), // Optional
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'admin',
            ]
        ]);

        // make seeder for booking
        DB::table('bookings')->insert([
            [

                'ticket_number' => 'TICKET-001',
                'name' => 'John Doe',
                'phone' => '08123456789',
                'address' => '123, Main Street, Lagos',
                'status' => 'approved',
                'message' => 'Approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ticket_number' => 'TICKET-002',
                'name' => 'Jane Smith',
                'phone' => '08123456789',
                'address' => '123, Main Street, Lagos',
                'status' => 'approved',
                'message' => 'Approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ticket_number' => 'TICKET-003',
                'name' => 'James Brown',
                'phone' => '08123456789',
                'address' => '123, Main Street, Lagos',
                'status' => 'approved',
                'message' => 'Approved',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        DB::table('availabilities')->insert([
            [
                'date' => '2023-10-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2024-10-21',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2024-10-22',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2024-10-23',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2024-10-24',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2024-10-25',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2024-10-26',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2024-10-27',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2024-10-28',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2024-9-29',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2024-9-30',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
