<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => '管理員',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

 
        Blog::factory(50)->create([
            'author_id' => $admin->id,  
        ]);
    }
}