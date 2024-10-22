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
    }
}
