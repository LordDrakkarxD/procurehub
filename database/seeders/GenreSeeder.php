<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $generos = [
            'Alimentício',            // 1
            'Medicamento',            // 2
            'Laboratório',            // 3
            'Material de Limpeza',    // 4
            'Material Odontológico',  // 5
            'Material Hospitalar',    // 6
            'Equipamentos',           // 7
            'Descartáveis',           // 8
            'Higiene Pessoal',        // 9
            'Cosmético',              // 10
            'Diversos',
        ];

        foreach ($generos as $nome) {
            Genre::firstOrCreate(
                ['name' => $nome]
            );
        }

    }
}