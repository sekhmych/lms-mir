<?php

namespace App\Http\Controllers;

use App\Models\ExternalRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExternalRequestController extends Controller
{
    public function index(): View
    {
        $requests = ExternalRequest::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('external-requests.index', compact('requests'));
    }

    public function create(): View
    {
        return view('external-requests.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'course_title' => ['required', 'string', 'max:255'],
            'program_link'  => ['nullable', 'url', 'max:2048'],
            'cost'          => ['required', 'numeric', 'min:0'],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['required', 'date', 'after_or_equal:start_date'],
            'description'   => ['nullable', 'string', 'max:2000'],
        ]);

        ExternalRequest::create([
            ...$data,
            'user_id' => auth()->id(),
            'status'  => 'pending',
        ]);

        return redirect()->route('external-requests.index')
            ->with('success', 'Заявка успешно создана.');
    }
}
