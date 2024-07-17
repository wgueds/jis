<?php

namespace Database\Seeders;

use App\Models\Bank;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Status;
use App\Models\ReleaseMethod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReleaseMethod::create(['name' => 'Único']);
        ReleaseMethod::create(['name' => 'Parcelado']);
        ReleaseMethod::create(['name' => 'Fixo']);

        Status::create(['name' => 'Criado']);
        Status::create(['name' => 'Agendado']);
        Status::create(['name' => 'Pago']);
        Status::create(['name' => 'Atrasado']);

        $user_1 = User::create([
            'name' => 'Wesley Guedes',
            'email' => 'wgueds@app.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('321321321'),
        ]);

        $user_2 = User::create([
            'name' => 'Joseane Guedes',
            'email' => 'joseane@app.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('321321321'),
        ]);

        $bank = Bank::create(['name' => 'Inter 001', 'description' => 'Conta teste']);

        $user_1->banks()->sync($bank->id);
        $user_2->banks()->sync($bank->id);
    }
}
