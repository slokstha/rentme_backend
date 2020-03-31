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
                'title' => 'If anybody need vehicle service for transportation means, please contact'.$i,
                'owner_name' => 'Luffy shrestha'.$i,
                'contact' => '9865098775'.$i,
                'service_area' => "SoltiDobato, Kalanki",
                'price' => 'Npr, 20000',
                'added_by'=>1,

            ]);
        }
    }
}
