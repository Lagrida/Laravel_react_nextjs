<?php

namespace Database\Seeders;

use App\Models\Link;
use App\Models\Product;
use Illuminate\Database\Seeder;

class LinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $productCount = Product::count();
        Link::factory(50)->create()->each(function(Link $link) use($productCount){
            $take = random_int(1, $productCount-5);
            $productIds = Product::inRandomOrder()->take($take)->get()->pluck('id');
            $link->products()->sync($productIds);
        });
    }
}
