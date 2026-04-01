<?php

namespace App\Services;

use App\Models\StepikCourse;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class StepikSyncService
{
    private const API_URL = 'https://stepik.org/api';
    private const MAX_LIST_PAGES = 20;
    private const CHUNK_SIZE = 10;
    private const MAX_COURSES_PER_SYNC = 300;

    public function sync(): int
    {
        try {
            $token = $this->fetchAccessToken();
            $courseMap = $this->fetchCourseMap($token);

            if ($courseMap === []) {
                Log::warning('Stepik sync skipped: empty course map from API.');

                return 0;
            }

            $records = [];
            $syncedIds = [];
            $failedChunks = 0;

            foreach (array_chunk(array_keys($courseMap), self::CHUNK_SIZE) as $chunk) {
                $courses = $this->fetchCoursesWithFallback($chunk, $token, $failedChunks);

                foreach ($courses as $course) {
                    if (! data_get($course, 'is_public', true)) {
                        continue;
                    }

                    $stepikId = (int) data_get($course, 'id');

                    if ($stepikId <= 0) {
                        continue;
                    }

                    $categories = $courseMap[$stepikId] ?? [];
                    $records[] = $this->normalizeCourse($course, $categories);
                    $syncedIds[] = $stepikId;
                }
            }

            if ($records === []) {
                Log::warning('Stepik sync finished with zero records.', [
                    'failed_chunks' => $failedChunks,
                    'mapped_ids' => count($courseMap),
                ]);

                return 0;
            }

            StepikCourse::query()->upsert(
                $records,
                ['stepik_id'],
                [
                    'title',
                    'summary',
                    'description',
                    'cover_url',
                    'course_url',
                    'price',
                    'display_price',
                    'currency_code',
                    'is_paid',
                    'difficulty',
                    'language',
                    'workload',
                    'categories',
                    'categories_text',
                    'learners_count',
                    'is_active',
                    'source_updated_at',
                    'last_synced_at',
                    'updated_at',
                ],
            );

            if ($failedChunks === 0) {
                StepikCourse::query()
                    ->whereNotIn('stepik_id', array_unique($syncedIds))
                    ->delete();
            } else {
                Log::warning('Stepik sync completed partially, skip stale delete.', [
                    'failed_chunks' => $failedChunks,
                    'synced_records' => count($records),
                ]);
            }

            return count($records);
        } catch (Throwable $exception) {
            Log::warning('Stepik sync failed', [
                'message' => $exception->getMessage(),
            ]);

            return 0;
        }
    }

    private function fetchAccessToken(): ?string
    {
        $clientId = (string) config('services.stepik.client_id');
        $clientSecret = (string) config('services.stepik.client_secret');

        if ($clientId === '' || $clientSecret === '') {
            return null;
        }

        $response = Http::asForm()
            ->connectTimeout(20)
            ->timeout(60)
            ->retry(3, 700, throw: false)
            ->post('https://stepik.org/oauth2/token/', [
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]);

        if (! $response->successful()) {
            Log::warning('Stepik token request failed', [
                'status' => $response->status(),
            ]);

            return null;
        }

        return $response->json('access_token');
    }

    private function fetchCourseMap(?string $token): array
    {
        $page = 1;
        $hasNext = true;
        $courseMap = [];

        while ($hasNext && $page <= self::MAX_LIST_PAGES) {
            $response = $this->request($token)
                ->get(self::API_URL.'/course-lists', ['page' => $page]);

            if (! $response->successful()) {
                Log::warning('Stepik course-lists request failed', [
                    'status' => $response->status(),
                    'page' => $page,
                ]);

                break;
            }

            $lists = $response->json('course-lists', []);

            foreach ($lists as $list) {
                if (data_get($list, 'language') !== 'ru') {
                    continue;
                }

                $title = trim((string) data_get($list, 'title'));

                if ($title === '') {
                    continue;
                }

                foreach (data_get($list, 'courses', []) as $courseId) {
                    $courseId = (int) $courseId;

                    if ($courseId <= 0) {
                        continue;
                    }

                    $courseMap[$courseId] ??= [];
                    $courseMap[$courseId][] = $title;

                    if (count($courseMap) >= self::MAX_COURSES_PER_SYNC) {
                        $hasNext = false;
                        break 2;
                    }
                }
            }

            $hasNext = (bool) $response->json('meta.has_next', false);
            $page++;
        }

        return collect($courseMap)
            ->map(fn (array $categories) => array_values(array_unique($categories)))
            ->all();
    }

    private function fetchCourses(array $courseIds, ?string $token): array
    {
        if ($courseIds === []) {
            return [];
        }

        $query = collect($courseIds)
            ->map(fn (int $id) => 'ids[]='.$id)
            ->implode('&');

        $response = $this->request($token)
            ->get(self::API_URL.'/courses?'.$query);

        if (! $response->successful()) {
            throw new \RuntimeException('Stepik courses request failed with status '.$response->status());
        }

        return $response->json('courses', []);
    }

    private function fetchCoursesWithFallback(array $courseIds, ?string $token, int &$failedChunks): array
    {
        try {
            return $this->fetchCourses($courseIds, $token);
        } catch (Throwable $exception) {
            $failedChunks++;

            Log::warning('Stepik sync chunk failed, switch to per-course fallback.', [
                'message' => $exception->getMessage(),
                'chunk_size' => count($courseIds),
                'first_id' => $courseIds[0] ?? null,
            ]);
        }

        $courses = [];

        foreach ($courseIds as $courseId) {
            try {
                $single = $this->fetchCourses([(int) $courseId], $token);

                if ($single !== []) {
                    $courses[] = $single[0];
                }

                // Reduce burst pressure on Stepik when fallback mode is active.
                usleep(120000);
            } catch (Throwable $exception) {
                Log::warning('Stepik fallback request failed for course.', [
                    'course_id' => $courseId,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return $courses;
    }

    private function normalizeCourse(array $course, array $categories): array
    {
        $now = now();
        $categories = array_values(array_unique(array_filter(array_map('trim', $categories))));

        return [
            'stepik_id' => (int) data_get($course, 'id'),
            'title' => (string) data_get($course, 'title'),
            'summary' => (string) data_get($course, 'summary', ''),
            'description' => (string) data_get($course, 'description', ''),
            'cover_url' => data_get($course, 'cover'),
            'course_url' => data_get($course, 'canonical_url') ?: 'https://stepik.org/course/'.data_get($course, 'id'),
            'price' => data_get($course, 'price'),
            'display_price' => (string) data_get($course, 'display_price', '-'),
            'currency_code' => data_get($course, 'currency_code'),
            'is_paid' => (bool) data_get($course, 'is_paid', false),
            'difficulty' => data_get($course, 'difficulty'),
            'language' => data_get($course, 'language'),
            'workload' => data_get($course, 'workload'),
            'categories' => json_encode($categories, JSON_UNESCAPED_UNICODE),
            'categories_text' => $categories === [] ? null : '|'.implode('|', $categories).'|',
            'learners_count' => (int) data_get($course, 'learners_count', 0),
            'is_active' => (bool) data_get($course, 'is_active', true),
            'source_updated_at' => $this->parseDate(data_get($course, 'update_date')),
            'last_synced_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function parseDate(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return Carbon::parse($value);
    }

    private function request(?string $token): PendingRequest
    {
        $request = Http::acceptJson()
            ->connectTimeout(20)
            ->timeout(60)
            ->retry(3, 700, throw: false);

        return $token ? $request->withToken($token) : $request;
    }
}
