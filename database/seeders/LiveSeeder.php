<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('photographers')->insert([
            'name' => 'Deepak Karki',
            'email' => 'deepakofficial@gmail.com',
            'phone' => '9841234567',
            'address' => 'Banasthali, Kathmandu',
            'description' => 'I am a professional photographer with 5 years of experience.',
            'facebook' => 'https://www.facebook.com/deepakofficial',
            'instagram' => 'https://www.instagram.com/deepakofficial',
            'youtube' => 'https://www.youtube.com/deepakoffcial',
        ]);
    }
}
