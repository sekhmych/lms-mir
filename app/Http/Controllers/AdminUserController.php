<?php

namespace App\Http\Controllers;

use App\Mail\UserCredentialsMail;
use App\Models\User;
use Dcblogdev\MsGraph\Models\MsGraphToken;
use Dcblogdev\MsGraph\MsGraph;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with('roles')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $msGraphUserIds = MsGraphToken::query()->pluck('user_id')->toArray();

        return view('admin.users.index', compact('users', 'msGraphUserIds'));
    }

    public function create(): View
    {
        $roles = Role::query()->orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'position' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
        ]);

        $plainPassword = Str::password(12, letters: true, numbers: true, symbols: false);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'position' => $validated['position'] ?? null,
            'password' => Hash::make($plainPassword),
        ]);

        $user->syncRoles([$validated['role']]);

        Mail::to($user->email)->send(new UserCredentialsMail($user, $plainPassword));

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Пользователь создан. Логин и пароль отправлены на почту.');
    }

    public function edit(User $user): View
    {
        $user->load('roles');
        $roles = Role::query()->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'position' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->position = $validated['position'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Данные пользователя обновлены.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Нельзя удалить текущего авторизованного пользователя.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Пользователь удалён.');
    }

    public function calendar(User $user): JsonResponse
    {
        $token = MsGraphToken::where('user_id', $user->id)->first();

        if (!$token) {
            return response()->json(['error' => 'У пользователя нет привязки Microsoft 365.'], 404);
        }

        try {
            $msgraph = new MsGraph();
            MsGraph::login($user);

            $now = now()->toIso8601String();
            $future = now()->addMonths(3)->toIso8601String();

            $response = $msgraph->get(
                "me/calendarView?startDateTime={$now}&endDateTime={$future}&\$orderby=start/dateTime&\$top=50&\$select=subject,start,end,location,isAllDay,webLink",
                [], [], $user->id
            );

            return response()->json([
                'events' => $response['value'] ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Не удалось загрузить календарь: ' . $e->getMessage()], 500);
        }
    }
}