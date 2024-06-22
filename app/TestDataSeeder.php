<?php

namespace Database\Seeders;

use App\Models\SaleStatus;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartItem;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductType;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        /**
         * Saller
         */
        $roleSeller = Role::where('name', 'seller')->first();
        $user = User::create([
            'name' => $faker->name(),
            'email' => 'seller@app.com',
            'document' => '61863632018',
            'postcode' => $faker->postcode,
            'address' => $faker->streetAddress,
            'number' => $faker->numberBetween(99, 9999),
            'complement' => $faker->sentence,
            'neighborhood' => $faker->name(),
            'city' => $faker->city,
            'state_id' => 25,
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('321321321'),
        ]);
        $user->addRole($roleSeller);

        /**
         * Categories
         */
        Category::create(['parent_id' => NULL, 'name' => 'Produtos digitais', 'description' => NULL, 'icon' => NULL, 'enable' => 1,]);
        $categoryEbook = Category::create(['parent_id' => 1, 'name' => 'eBook', 'description' => NULL, 'icon' => NULL, 'enable' => 1,]);
        Category::create(['parent_id' => 1, 'name' => 'Imagem / Foto', 'description' => NULL, 'icon' => NULL, 'enable' => 1,]);
        Category::create(['parent_id' => 1, 'name' => 'Áudio / Música', 'description' => NULL, 'icon' => NULL, 'enable' => 1,]);
        Category::create(['parent_id' => 1, 'name' => 'Vídeo / Filme', 'description' => NULL, 'icon' => NULL, 'enable' => 1,]);
        Category::create(['parent_id' => 1, 'name' => 'Arquivo', 'description' => NULL, 'icon' => NULL, 'enable' => 1,]);

        /**
         * Product types
         */
        $typeFile = ProductType::create(['name' => 'file', 'description' => 'Arquivo para download', 'enable' => 1]);

        /**
         * Products
         */
        $product1 = Product::create([
            'product_type_id' => $typeFile->id,
            'name' => 'Laravel Queues',
            'resume' => 'O poder das filas em suas mãos',
            'description' => 'Descubra o poder das filas com Laravel Queues! Aprenda a otimizar tarefas assíncronas, melhorar o desempenho de suas aplicações e tornar seus processos mais eficientes. Domine o uso de filas em Laravel.',
            'price' => 59.90,
        ]);
        $product2 = Product::create([
            'product_type_id' => $typeFile->id,
            'name' => 'Laravel Queues v2',
            'resume' => 'O poder das filas em suas mãos',
            'description' => 'Descubra o poder das filas com Laravel Queues! Aprenda a otimizar tarefas assíncronas, melhorar o desempenho de suas aplicações e tornar seus processos mais eficientes. Domine o uso de filas em Laravel.',
            'price' => 69.90,
        ]);

        $user->products()->attach($product1->id, ['quantity' => 100, 'limit' => 10]);
        $product1->categories()->attach($categoryEbook->id);

        $user->products()->attach($product2->id, ['quantity' => 100, 'limit' => 10]);
        $product2->categories()->attach($categoryEbook->id);

        /**
         * Sale status
         */
        SaleStatus::create(['name' => 'Open']);
        SaleStatus::create(['name' => 'Pending']);
        SaleStatus::create(['name' => 'Done']);
        SaleStatus::create(['name' => 'Canceled']);
        SaleStatus::create(['name' => 'Error']);

        /**
         * Shopping cart
         */
        $cart = ShoppingCart::create(['name' => 'Venda Teste', 'code' => Str::uuid(), 'total' => 129.8]);
        ShoppingCartItem::create([
            'shopping_cart_id' => $cart->id,
            'users_has_product_id' => 1,
            'unity_price' => $product1->price,
            'quantity' => 1,
        ]);
        ShoppingCartItem::create([
            'shopping_cart_id' => $cart->id,
            'users_has_product_id' => 2,
            'unity_price' => $product2->price,
            'quantity' => 1,
        ]);
    }
}
