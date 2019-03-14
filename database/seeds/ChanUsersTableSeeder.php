<?php

use Illuminate\Database\Seeder;

class ChanUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\ChanUser::class, 20)->create();
    }
}
