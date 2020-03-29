<?php

use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <=5 ; $i++) {
            \App\Post::insert([
                'location' => 'kamalpokhari',
                'description' => 'Danger Room',
                'user_id' => $i,
                'title' => 'Room is on sale',
                'status' => 0,
                'city' => 'Kathmandu',
                'price' => 8000,
                'property_type' => 'Room',
                'facilities' => 'water 24 available',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),

            ]);
        }
    }

}
