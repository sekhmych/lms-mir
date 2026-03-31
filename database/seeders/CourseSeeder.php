<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Direction;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {

        $corporate_program = Direction::where('name', '=', 'Корпоративная программа')->first();
        $personal_effectiveness = Direction::where('name', '=', 'Личная эффективность')->first();
        $managerial_effectiveness = Direction::where('name', '=', 'Управленческая эффективность')->first();
        $communication_effectiveness = Direction::where('name', '=', 'Коммуникативная эффективность')->first();
        $digital_efficiency = Direction::where('name', '=', 'Цифровая эффективность')->first();


        Course::create([
            'title' => 'Введение в корпоративную культуру',
            'description' => 'Познакомьтесь с ценностями и политиками компании.',
            'direction_id' => $corporate_program?->id,
            'type' => 'internal',
            'price' => 0,
            'duration' => 2,
            'external_link' => null,
        ]);

        Course::create([
            'title' => 'Личный тайм-менеджмент',
            'description' => 'Освойте основы тайм-менеджмента и лучшие практики от наших ТОПов.',
            'direction_id' => $personal_effectiveness?->id,
            'type' => 'internal',
            'price' => 0,
            'duration' => 3,
            'external_link' => null,
        ]);

        Course::create([
            'title' => 'Управление отделом от А до Я',
            'description' => 'Повысьте эффективность доверенного отдела и получите наконец повышение.',
            'direction_id' => $managerial_effectiveness?->id,
            'type' => 'internal',
            'price' => 0,
            'duration' => 2,
            'external_link' => null,
        ]);

        Course::create([
            'title' => 'Публичные выступления на планёрке',
            'description' => 'Научитесь переводить темы с вас на ваших коллег.',
            'direction_id' => $communication_effectiveness->id,
            'type' => 'internal',
            'price' => 0,
            'duration' => 4,
            'external_link' => null,
        ]);

        Course::create([
            'title' => 'Автоматизация проверки отчётов Excel',
            'description' => 'Перед тем как кидать свои недоотчёты руководителю, научим вас проверять их через GigaChat всего двумя кликами',
            'direction_id' => $digital_efficiency->id,
            'type' => 'internal',
            'price' => 0,
            'duration' => 1,
            'external_link' => null,
        ]);

    }
}
