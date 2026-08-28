<?php

namespace Database\Seeders;

use App\Models\Merchandise;
use Illuminate\Database\Seeder;

class MerchandiseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merchandises = [
            [
                'name'            => 'Tumbler Eksklusif',
                'points'          => 150,
                'points_required' => 150,
                'stock'           => 30,
                'is_active'       => true,
                'image'           => 'tumbler_hadiah_1787316639461.jpg',
                'description'     => 'Tumbler botol minum premium anti-panas dengan desain RSI Sakinah.',
            ],
            [
                'name'            => 'Piring Cantik',
                'points'          => 200,
                'points_required' => 200,
                'stock'           => 25,
                'is_active'       => true,
                'image'           => 'piring_hadiah_1787316666659.jpg',
                'description'     => 'Piring keramik elegan dengan corak bunga klasik.',
            ],
            [
                'name'            => 'Payung Golf Premium',
                'points'          => 250,
                'points_required' => 250,
                'stock'           => 20,
                'is_active'       => true,
                'image'           => 'payung_hadiah_1787316678961.jpg',
                'description'     => 'Payung ukuran besar, kokoh, dan elegan.',
            ],
            [
                'name'            => 'Mug Keramik Minimalis',
                'points'          => 100,
                'points_required' => 100,
                'stock'           => 40,
                'is_active'       => true,
                'image'           => 'mug_hadiah_1787316690868.jpg',
                'description'     => 'Mug kopi keramik berkualitas dengan logo rumah sakit.',
            ],
            [
                'name'            => 'Kaos RSI Sakinah',
                'points'          => 500,
                'points_required' => 500,
                'stock'           => 15,
                'is_active'       => true,
                'image'           => 'kaos_hadiah_1787316703321.jpg',
                'description'     => 'Kaos cotton combed premium eksklusif edisi spesial.',
            ],
        ];

        foreach ($merchandises as $item) {
            Merchandise::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
