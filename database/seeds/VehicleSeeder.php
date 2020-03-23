<?php

use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i <=5 ; $i++) {
            \App\Vehicle::insert([
                'title' => 'kamalpokhari'.$i,
                'description' => 'Danger Room',
                'owner_name' => 'anita shrestha'.$i,
                'contact' => '9865098775'.$i,
                'service_area' => 1,
                'price' => 'Npr, 20000',
                'added_by'=>1,

            ]);
        }
    }
}
