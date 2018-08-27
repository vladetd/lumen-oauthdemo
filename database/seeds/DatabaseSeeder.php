<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('oauth_clients')->insert([
            ['name' => 'app1'], ['name' => 'app2']
        ]);


        DB::table('oauth_scopes')->insert([
            ['name' => 'member'], ['name' => 'client'], ['name' => 'partner']
        ]);

        DB::table('members')->insert([
            ['username' => 'vladetd', 'password' => Hash::make('password')]
        ]);
    }
}
