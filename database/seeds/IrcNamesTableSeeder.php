<?php

use Illuminate\Database\Seeder;

class IrcNamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\IrcName::class, 50)->create();
    }
}
