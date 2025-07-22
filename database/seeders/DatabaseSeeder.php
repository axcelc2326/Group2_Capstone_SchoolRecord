<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call([SubjectSeeder::class,]);
        // $this->call(ClassSeeder::class);
        $this->call(ParentsWithChildrenSeeder::class);
    }
}
