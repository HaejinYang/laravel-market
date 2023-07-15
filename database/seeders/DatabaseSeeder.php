<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Category::truncate();
        Product::truncate();
        Transaction::truncate();
        DB::table('category_product')->truncate();
        Schema::enableForeignKeyConstraints();

        $usersQuantity = 200;
        $categoriesQuantity = 30;
        $productsQuantity = 1000;
        $transactionsQuantity = 1000;

        User::factory()->count($usersQuantity)->create();
        Category::factory()->count($categoriesQuantity)->create();
        Product::factory()->count($productsQuantity)->create()->each(function ($product) {
            $categories = Category::all()->random(mt_rand(1, 5))->pluck('id');
            $product->categories()->attach($categories);
        });
        Transaction::factory()->count($transactionsQuantity)->create();
    }
}
