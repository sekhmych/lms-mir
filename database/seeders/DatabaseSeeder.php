<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'hr']);
        Role::create(['name' => 'director']);
        Role::create(['name' => 'employee']);
        Role::create(['name' => 'trainer']);


        // Создаём админса
        $admin = User::factory()->create([
            'name' => 'Сергеев Владимир Дмитриевич',
            'email' => 'admin@alrosa.ru',
            'password' => 'admin',
            'position' => 'Админ',
        ]);
        $admin->assignRole('admin');

        // Обучаем эйчара
        $hr = User::factory()->create([
            'name' => 'Антонов Артур Игоревич',
            'email' => 'hr@alrosa.ru',
            'password' => 'hr',
            'position' => 'Эйчар 2 офиса',
        ]);
        $hr->assignRole('hr');

        // Добавляем руководителя
        $director = User::factory()->create([
            'name' => 'Иванцов Данил Владимирович',
            'email' => 'director@alrosa.ru',
            'password' => 'director',
            'position' => 'Директор головного офиса',
        ]);
        $director->assignRole('director');

        // Вспоминаем про сотрудника
        $employee = User::factory()->create([
            'name' => 'Горохов Станислав Вадимович',
            'email' => 'employee@alrosa.ru',
            'password' => 'employee',
            'position' => 'Юрист 5 отдела 2 офиса',
        ]);
        $employee->assignRole('employee');

        // Гоняем тренера
        $trainer = User::factory()->create([
            'name' => 'Осипов Максим Егорович',
            'email' => 'trainer@alrosa.ru',
            'password' => 'trainer',
            'position' => 'Тренер по реляционным базам данных',
        ]);
        $trainer->assignRole('trainer');


        $this->call(DirectionSeeder::class);
        $this->call(CourseSeeder::class);

    }
}
