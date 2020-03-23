<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0;$i<5;$i++) {
            DB::table('users')->insert([
                'name' => 'Anita Shrestha',
                'email' => 'anita'.$i.'34@gmail.com',
                'password' => bcrypt('12345'),
                'address' => ('Tinkune, Kathmandu'),
                'phone' => ('986509877'.$i),
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ]);
        }
        $this->call(PostSeeder::class);
        $this->call(VehicleSeeder::class);
    }
}
