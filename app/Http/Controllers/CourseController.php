<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\StepikCourse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CourseController extends Controller
{
    private const PER_PAGE = 6;

    public function show(Course $course): View
    {
        $course->load(['direction', 'sessions' => fn($q) => $q->where('status', '!=', 'cancelled')->orderBy('start_date')]);

        return view('courses.show', compact('course'));
    }

    public function index(Request $request): View
    {
        $filters = [
            'type' => $request->string('type')->toString() ?: 'all',
            'price' => $request->string('price')->toString() ?: 'all',
            'category' => trim($request->string('category')->toString()),
        ];

        if (! in_array($filters['type'], ['all', 'internal', 'external'], true)) {
            $filters['type'] = 'all';
        }

        if (! in_array($filters['price'], ['all', 'free', 'paid'], true)) {
            $filters['price'] = 'all';
        }

        $courses = $this->getCourses($filters, $request);
        $stepikCategories = StepikCourse::query()
            ->whereNotNull('categories')
            ->get()
            ->pluck('categories')
            ->flatten()
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('courses.index', [
            'filters' => $filters,
            'courses' => $courses,
            'stepikCategories' => $stepikCategories,
        ]);
    }

    private function getCourses(array $filters, Request $request): LengthAwarePaginator
    {
        if ($filters['type'] === 'internal') {
            return $this->tagPaginatorCollection(
                $this->getInternalCoursesQuery($filters)->paginate(self::PER_PAGE)->withQueryString(),
                'internal',
            );
        }

        if ($filters['type'] === 'external') {
            return $this->tagPaginatorCollection(
                $this->getStepikCoursesQuery($filters)->paginate(self::PER_PAGE)->withQueryString(),
                'external',
            );
        }

        $page = LengthAwarePaginator::resolveCurrentPage();
        $internalCourses = $this->getInternalCoursesQuery($filters)
            ->get()
            ->map(fn (Course $course) => $this->tagCourse($course, 'internal'));
        $stepikCourses = $this->getStepikCoursesQuery($filters)
            ->get()
            ->map(fn (StepikCourse $course) => $this->tagCourse($course, 'external'));
        $items = $internalCourses
            ->concat($stepikCourses)
            ->sortBy(fn (Model $course) => mb_strtolower((string) $course->title))
            ->values();

        return new LengthAwarePaginator(
            $items->forPage($page, self::PER_PAGE)->values(),
            $items->count(),
            self::PER_PAGE,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ],
        );
    }

    private function getInternalCoursesQuery(array $filters): Builder
    {
        $query = Course::query()
            ->where('type', 'internal')
            ->with(['direction', 'sessions' => fn($q) => $q->where('status', '!=', 'cancelled')->orderBy('start_date')])
            ->orderBy('title');

        $this->applyInternalPriceFilter($query, $filters['price']);

        return $query;
    }

    private function getStepikCoursesQuery(array $filters)
    {
        $query = StepikCourse::query()
            ->where('is_active', true)
            ->orderByDesc('learners_count')
            ->orderBy('title');

        if ($filters['category'] !== '') {
            $query->where('categories_text', 'like', '%|'.$filters['category'].'|%');
        }

        if ($filters['price'] === 'free') {
            $query->where('is_paid', false);
        }

        if ($filters['price'] === 'paid') {
            $query->where('is_paid', true);
        }

        return $query;
    }

    private function applyInternalPriceFilter(Builder $query, string $price): void
    {
        if ($price === 'free') {
            $query->where(function (Builder $builder) {
                $builder->whereNull('price')->orWhere('price', '<=', 0);
            });
        }

        if ($price === 'paid') {
            $query->where('price', '>', 0);
        }
    }

    private function tagPaginatorCollection(LengthAwarePaginator $paginator, string $source): LengthAwarePaginator
    {
        $paginator->setCollection(
            $paginator->getCollection()->map(fn (Model $course) => $this->tagCourse($course, $source))
        );

        return $paginator;
    }

    private function tagCourse(Model $course, string $source): Model
    {
        $course->setAttribute('source_type', $source);

        return $course;
    }
}
