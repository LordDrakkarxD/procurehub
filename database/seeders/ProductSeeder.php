<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $produtos = [
            [
                'name' => 'Leite em Pó Integral 400g',
                'genre_id' => 1,
                'price' => 28.90,
                'unit' => 'UN',
                'description' => 'Leite em pó integral, embalagem de 400g.',
            ],
            [
                'name' => 'Suplemento Nutricional 400g',
                'genre_id' => 1,
                'price' => 64.50,
                'unit' => 'UN',
                'description' => 'Complemento alimentar em pó, sabor baunilha.',
            ],
            [
                'name' => 'Dipirona 500mg 20 comprimidos',
                'genre_id' => 2,
                'price' => 8.90,
                'unit' => 'CX',
                'description' => 'Analgésico e antitérmico. Caixa com 20 comprimidos.',
            ],
            [
                'name' => 'Paracetamol 750mg 20 comprimidos',
                'genre_id' => 2,
                'price' => 12.40,
                'unit' => 'CX',
                'description' => 'Analgésico e antitérmico. Caixa com 20 comprimidos.',
            ],
            [
                'name' => 'Tubo de Ensaio 10ml',
                'genre_id' => 3,
                'price' => 1.20,
                'unit' => 'UN',
                'description' => 'Tubo de ensaio em vidro, capacidade 10ml.',
            ],
            [
                'name' => 'Lâmina para Microscópio',
                'genre_id' => 3,
                'price' => 18.00,
                'unit' => 'CX',
                'description' => 'Caixa com 50 lâminas para microscopia.',
            ],
            [
                'name' => 'Álcool 70% 1L',
                'genre_id' => 4,
                'price' => 9.90,
                'unit' => 'UN',
                'description' => 'Álcool etílico hidratado 70% INPM, frasco 1 litro.',
            ],
            [
                'name' => 'Detergente Neutro 5L',
                'genre_id' => 4,
                'price' => 32.00,
                'unit' => 'UN',
                'description' => 'Detergente neutro concentrado para limpeza geral.',
            ],
            [
                'name' => 'Resina Composta A2 4g',
                'genre_id' => 5,
                'price' => 48.90,
                'unit' => 'UN',
                'description' => 'Resina composta fotopolimerizável, cor A2.',
            ],
            [
                'name' => 'Alginato para Moldagem 454g',
                'genre_id' => 5,
                'price' => 36.50,
                'unit' => 'UN',
                'description' => 'Alginato cromático para moldagem odontológica.',
            ],
            [
                'name' => 'Soro Fisiológico 0,9% 500ml',
                'genre_id' => 6,
                'price' => 6.80,
                'unit' => 'UN',
                'description' => 'Solução de cloreto de sódio 0,9%, frasco 500ml.',
            ],
            [
                'name' => 'Gaze Estéril 10x10cm',
                'genre_id' => 6,
                'price' => 14.90,
                'unit' => 'PCT',
                'description' => 'Pacote com 10 compressas de gaze estéril.',
            ],
            [
                'name' => 'Oxímetro de Pulso Digital',
                'genre_id' => 7,
                'price' => 89.90,
                'unit' => 'UN',
                'description' => 'Oxímetro portátil para medição de SpO2 e pulso.',
            ],
            [
                'name' => 'Balança Digital 5kg',
                'genre_id' => 7,
                'price' => 159.00,
                'unit' => 'UN',
                'description' => 'Balança de precisão para laboratório e farmácia.',
            ],
            [
                'name' => 'Luva de Procedimento M',
                'genre_id' => 8,
                'price' => 24.90,
                'unit' => 'CX',
                'description' => 'Caixa com 100 luvas de procedimento, tamanho M.',
            ],
            [
                'name' => 'Máscara Cirúrgica Tripla',
                'genre_id' => 8,
                'price' => 19.90,
                'unit' => 'CX',
                'description' => 'Caixa com 50 máscaras descartáveis tripla camada.',
            ],
            [
                'name' => 'Sabonete Líquido 500ml',
                'genre_id' => 9,
                'price' => 12.50,
                'unit' => 'UN',
                'description' => 'Sabonete líquido para higiene das mãos.',
            ],
            [
                'name' => 'Creme Dental 90g',
                'genre_id' => 9,
                'price' => 6.90,
                'unit' => 'UN',
                'description' => 'Creme dental com flúor, tubo 90g.',
            ],
            [
                'name' => 'Protetor Solar FPS 50 120g',
                'genre_id' => 10,
                'price' => 54.90,
                'unit' => 'UN',
                'description' => 'Protetor solar facial e corporal FPS 50.',
            ],
            [
                'name' => 'Hidratante Facial 50g',
                'genre_id' => 10,
                'price' => 39.90,
                'unit' => 'UN',
                'description' => 'Hidratante facial para peles secas e sensíveis.',
            ],
            [
                'name' => 'Fita Crepe 18mm x 50m',
                'genre_id' => 11,
                'price' => 7.50,
                'unit' => 'UN',
                'description' => 'Fita crepe para uso geral.',
            ],
            [
                'name' => 'Pilha Alcalina AA',
                'genre_id' => 11,
                'price' => 14.90,
                'unit' => 'PAR',
                'description' => 'Par de pilhas alcalinas AA.',
            ],
        ];

        foreach ($produtos as $produto) {
            Product::firstOrCreate(
                ['name' => $produto['name']],
                $produto
            );
        }
        

            
    }
}