<?php

namespace Database\Seeders;

use App\Models\Direction;
use Illuminate\Database\Seeder;

class DirectionSeeder extends Seeder
{
    public function run(): void
    {
        $directions = [
            [
                'name' => 'Корпоративная программа',
                'description' => 'Курс по корпоративной культуре, политике и ценностям.'
            ],
            [
                'name' => 'Личная эффективность',
                'description' => 'Управление временем и саморазвитие.'
            ],
            [
                'name' => 'Управленческая эффективность',
                'description' => 'Навыки управления командой и проектами.'
            ],
            [
                'name' => 'Коммуникативная эффективность',
                'description' => 'Навыки публичных выступлений и переговоров.'
            ],
            [
                'name' => 'Корпоративная эффективность',
                'description' => 'Повышаемся до ГенДира.'
            ],
            [
                'name' => 'Цифровая эффективность',
                'description' => 'Работа с цифровыми инструментами и автоматизация.'
            ],
        ];

        foreach ($directions as $dir) {
            Direction::create($dir);
        }
    }
}
