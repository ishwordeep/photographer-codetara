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

        DB::table('categories')->insert(
            [
                [
                    'name' => 'Photography',
                    'description' => 'Photography is the art, application, and practice of creating durable images by recording light, either electronically by means of an image sensor, or chemically by means of a light-sensitive material such as photographic film.',
                    'is_active' => 1,
                    'slug' => 'photography',
                ],
                [
                    'name' => 'Videography',
                    'description' => 'Videography refers to the process of capturing moving images on electronic media and even streaming media.',
                    'is_active' => 1,
                    'slug' => 'videography',
                ],
                [
                    'name' => 'Wedding',
                    'description' => 'Wedding photography is the photography of activities relating to weddings. It encompasses photographs of the couple before marriage as well as coverage of the wedding and reception.',
                    'is_active' => 1,
                    'slug' => 'wedding',
                ],
                [
                    'name' => 'Fashion',
                    'description' => 'Fashion photography is a genre of photography that is devoted to displaying clothing and other fashion items.',
                    'is_active' => 1,
                    'slug' => 'fashion',
                ],
                [
                    'name' => 'Event',
                    'description' => 'Event photography is the practice of photographing guests and occurrences at any Event or occasion where one may hire a photographer for.',
                    'is_active' => 1,
                    'slug' => 'event',
                ],
                [
                    'name' => 'Product',
                    'description' => 'Product photography is a branch of commercial photography which is about accurately but attractively representing a product.',
                    'is_active' => 1,
                    'slug' => 'product',
                ],
            ]

        );
    }
}
