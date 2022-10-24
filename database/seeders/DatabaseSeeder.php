<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
         \App\Models\User::factory(10)->create();
        $user = new \App\Models\User();
        $user->email = 'mflores@gmail.com';
        $user->name = 'Mauro Flores';
        $user->password = Hash::make('paswword');
        $user->save();
    }
}
