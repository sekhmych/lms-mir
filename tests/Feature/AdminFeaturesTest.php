<?php

use App\Mail\UserCredentialsMail;
use App\Models\Course;
use App\Models\StepikCourse;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

test('admin sees dashboard metrics and sync action', function () {
    Role::findOrCreate('admin');

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Course::unguarded(fn () => Course::query()->create([
        'title' => 'Внутренний курс',
        'type' => 'internal',
    ]));

    StepikCourse::query()->create([
        'stepik_id' => 99,
        'title' => 'Курс Stepik',
        'course_url' => 'https://stepik.org/course/99/',
        'display_price' => '-',
        'is_paid' => false,
        'learners_count' => 25,
        'is_active' => true,
        'last_synced_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Панель администратора');
    $response->assertSee('Свободное место на диске');
    $response->assertSee('Всего курсов');
    $response->assertSee('Синхронизировать курсы Stepik');
});

test('admin can create user with generated password and send mail', function () {
    Mail::fake();

    Role::findOrCreate('admin');
    Role::findOrCreate('employee');

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this
        ->actingAs($admin)
        ->post(route('admin.users.store'), [
            'name' => 'Иван Петров',
            'email' => 'ivan.petrov@example.com',
            'position' => 'Специалист',
            'role' => 'employee',
        ]);

    $response->assertRedirect(route('admin.users.index'));

    $user = User::query()->where('email', 'ivan.petrov@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->roles->pluck('name')->all())->toBe(['employee']);

    Mail::assertSent(UserCredentialsMail::class, function (UserCredentialsMail $mail) use ($user) {
        return $mail->user->is($user) && $mail->plainPassword !== '';
    });
});