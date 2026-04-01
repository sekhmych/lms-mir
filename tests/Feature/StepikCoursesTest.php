<?php

use App\Models\Course;
use App\Models\Direction;
use App\Models\StepikCourse;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;

test('stepik sync command stores and updates courses in database', function () {
    config()->set('services.stepik.client_id', 'client-id');
    config()->set('services.stepik.client_secret', 'client-secret');

    Http::fake([
        'https://stepik.org/oauth2/token/' => Http::response([
            'access_token' => 'test-token',
        ]),
        'https://stepik.org/api/course-lists*' => Http::response([
            'meta' => ['has_next' => false],
            'course-lists' => [
                [
                    'title' => 'Программирование и разработка',
                    'language' => 'ru',
                    'courses' => [63054],
                ],
            ],
        ]),
        'https://stepik.org/api/courses*' => Http::response([
            'courses' => [
                [
                    'id' => 63054,
                    'title' => 'Интерактивный тренажер по SQL',
                    'summary' => 'Практический курс по SQL.',
                    'description' => '<p>Описание курса</p>',
                    'cover' => 'https://cdn.stepik.net/sql.png',
                    'canonical_url' => 'https://stepik.org/course/63054/',
                    'price' => null,
                    'display_price' => '-',
                    'currency_code' => null,
                    'is_paid' => false,
                    'difficulty' => 'easy',
                    'language' => 'ru',
                    'workload' => '3-6 часов в неделю',
                    'learners_count' => 12345,
                    'is_active' => true,
                    'is_public' => true,
                    'update_date' => '2026-04-01T10:00:00Z',
                ],
            ],
        ]),
    ]);

    $this->artisan('stepik:sync')
        ->expectsOutput('Synchronized 1 Stepik courses.')
        ->assertSuccessful();

    $course = StepikCourse::query()->first();

    expect($course)->not->toBeNull();
    expect($course->stepik_id)->toBe(63054);
    expect($course->title)->toBe('Интерактивный тренажер по SQL');
    expect($course->categories)->toBe(['Программирование и разработка']);
});

test('courses page filters internal and external courses from database', function () {
    $role = Role::findOrCreate('employee');
    $user = User::factory()->create();
    $user->assignRole($role);

    $direction = Direction::unguarded(fn () => Direction::query()->create([
        'name' => 'Разработка',
    ]));

    Course::unguarded(fn () => Course::query()->create([
        'title' => 'Внутренний бесплатный курс',
        'description' => 'Описание',
        'direction_id' => $direction->id,
        'type' => 'internal',
        'price' => 0,
        'duration' => 12,
    ]));

    Course::unguarded(fn () => Course::query()->create([
        'title' => 'Внутренний платный курс',
        'description' => 'Описание',
        'direction_id' => $direction->id,
        'type' => 'internal',
        'price' => 1500,
        'duration' => 18,
    ]));

    StepikCourse::query()->create([
        'stepik_id' => 1,
        'title' => 'Бесплатный курс Stepik',
        'summary' => 'Описание Stepik',
        'course_url' => 'https://stepik.org/course/1/',
        'display_price' => '-',
        'is_paid' => false,
        'categories' => ['Анализ данных и AI'],
        'categories_text' => '|Анализ данных и AI|',
        'learners_count' => 100,
        'is_active' => true,
        'last_synced_at' => now(),
    ]);

    StepikCourse::query()->create([
        'stepik_id' => 2,
        'title' => 'Платный курс Stepik',
        'summary' => 'Описание Stepik',
        'course_url' => 'https://stepik.org/course/2/',
        'display_price' => '2 500 ₽',
        'price' => 2500,
        'is_paid' => true,
        'categories' => ['Бизнес-курсы'],
        'categories_text' => '|Бизнес-курсы|',
        'learners_count' => 50,
        'is_active' => true,
        'last_synced_at' => now(),
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('courses.index', [
            'type' => 'all',
            'price' => 'free',
            'category' => 'Анализ данных и AI',
        ]));

    $response->assertOk();
    $response->assertSee('Внутренний бесплатный курс');
    $response->assertSee('Бесплатный курс Stepik');
    $response->assertDontSee('Внутренний платный курс');
    $response->assertDontSee('Платный курс Stepik');
});

test('courses page uses shared pagination for mixed course list', function () {
    $role = Role::findOrCreate('employee');
    $user = User::factory()->create();
    $user->assignRole($role);

    $direction = Direction::unguarded(fn () => Direction::query()->create([
        'name' => 'Аналитика',
    ]));

    foreach (range(1, 4) as $number) {
        Course::unguarded(fn () => Course::query()->create([
            'title' => 'A-Internal-'.$number,
            'description' => 'Описание',
            'direction_id' => $direction->id,
            'type' => 'internal',
            'price' => 0,
            'duration' => 10,
        ]));
    }

    foreach (range(1, 4) as $number) {
        StepikCourse::query()->create([
            'stepik_id' => 100 + $number,
            'title' => 'B-Stepik-'.$number,
            'summary' => 'Описание Stepik',
            'course_url' => 'https://stepik.org/course/'.(100 + $number).'/',
            'display_price' => '-',
            'is_paid' => false,
            'categories' => ['Data'],
            'categories_text' => '|Data|',
            'learners_count' => 100 - $number,
            'is_active' => true,
            'last_synced_at' => now(),
        ]);
    }

    $firstPage = $this
        ->actingAs($user)
        ->get(route('courses.index', ['type' => 'all']));

    $firstPage->assertOk();
    $firstPage->assertSee('A-Internal-1');
    $firstPage->assertSee('A-Internal-4');
    $firstPage->assertSee('B-Stepik-1');
    $firstPage->assertSee('?page=2', false);
    $firstPage->assertDontSee('B-Stepik-3');
    $firstPage->assertDontSee('B-Stepik-4');

    $secondPage = $this
        ->actingAs($user)
        ->get(route('courses.index', ['type' => 'all', 'page' => 2]));

    $secondPage->assertOk();
    $secondPage->assertSee('B-Stepik-3');
    $secondPage->assertSee('B-Stepik-4');
    $secondPage->assertDontSee('A-Internal-1');
});