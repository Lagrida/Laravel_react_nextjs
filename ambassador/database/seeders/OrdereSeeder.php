<?php

namespace Database\Seeders;

use App\Models\Ordere;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrdereSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Ordere::factory(35)->create()->each(function(Ordere $ordere){
            OrderItem::factory(random_int(0, 4))->create(
                [
                    'ordere_id' => $ordere->id
                ]
            );
        });
    }
}
