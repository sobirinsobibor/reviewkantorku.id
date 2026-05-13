<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Wahyu Sobirin',
            'username' => 'Mr. Griffin',
            'email' => 'wsobirin2@gmail.com',
            'password' => Hash::make('password'),
            'is_admin' => 1,
            'ulid' => Str::ulid()
        ]);

        User::factory()->create([
            'name' => 'Imam Wahyu Sobirin',
            'username' => 'Quagmire',
            'email' => 'imamwsobirin@gmail.com',
            'password' => Hash::make('password'),
            'is_admin' => 0,
            'ulid' => Str::ulid()
        ]);

        $this->call([
            IndustrySeeder::class,
            ContentFormSeeder::class
        ]);
    }
}
