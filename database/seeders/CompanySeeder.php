<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $empresas = [
            [
                'name' => 'Farma Norte Distribuidora Ltda',
                'trade_name' => 'Farma Norte',
                'document' => '12345678000190',
                'email' => 'contato@farmanorte.com',
                'phone' => '11987654321',
            ],
            [
                'name' => 'Lab Vida Análises Clínicas Ltda',
                'trade_name' => 'Lab Vida',
                'document' => '23456789000101',
                'email' => 'contato@labvida.com',
                'phone' => '21976543210',
            ],
            [
                'name' => 'Odonto Prime Materiais Ltda',
                'trade_name' => 'Odonto Prime',
                'document' => '34567890000112',
                'email' => 'vendas@odontoprime.com',
                'phone' => '31965432109',
            ],
            [
                'name' => 'Clean Med Higiene e Limpeza Ltda',
                'trade_name' => 'Clean Med',
                'document' => '45678901000123',
                'email' => 'contato@cleanmed.com',
                'phone' => '41954321098',
            ],
            [
                'name' => 'Equipamed Equipamentos Hospitalares Ltda',
                'trade_name' => 'Equipamed',
                'document' => '56789012000134',
                'email' => 'comercial@equipamed.com',
                'phone' => '51943210987',
            ],
        ];

        foreach ($empresas as $empresa) {
            Company::firstOrCreate(
                ['document' => $empresa['document']],
                $empresa
            );
        }

            
    }
}