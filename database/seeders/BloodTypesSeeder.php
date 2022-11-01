<?php

namespace Database\Seeders;

use App\Models\BloodTypes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BloodTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $types = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];

       foreach($types as $type){
        BloodTypes::create([
            "type" => $type,
            "amount" => 0,
        ]);
       }
    }
}
