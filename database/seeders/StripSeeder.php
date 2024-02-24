<?php

    namespace Database\Seeders;
    use App\Models\Strip;

    use Illuminate\Database\Seeder;

    class StripSeeder extends Seeder{
        public function run(){
            $unit = array('inch', 'feet', 'meter');

            for($i=1; $i<=3; $i++){
                Strip::create([
                    'name' => "Light $i",
                    'quantity' => $i,
                    'unit' => $unit[$i-1],
                    'choke' => $i,
                    'price' => 5 * $i,
                    'amp' => $i,
                    'inch_price' => $unit[$i-1] != 'inch' ? _converter($unit[$i-1], $i) / (5 * $i) : 5 * $i,
                    'note' => "lorem ipsum $i",
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => 1
                ]);
            }
        }
    }
