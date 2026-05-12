<?php

namespace App\Services;

use App\Exceptions\DuplicateSubjectException;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Subject service layer.
 * Per 11_backend_architecture_laravel.md §5
 */
class SubjectService
{
    /**
     * Get paginated subjects with filters.
     */
    public function getSubjects(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Subject::query()
            ->withTrashed()
            ->with('latestMeasurement')
            ->withCount('measurements');

        // RBAC: Removed user_id restriction to allow global search for duplicate prevention.
        // History (Measurements) remains restricted in MeasurementService.

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        // Category filter (calculated based on age)
        if (!empty($filters['category'])) {
            $today = now()->toDateString();
            $query->where(function ($q) use ($filters, $today) {
                $category = $filters['category'];
                if ($category === 'balita') {
                    // Age <= 60 months (5 years ago)
                    $q->where('date_of_birth', '>=', Carbon::parse($today)->subMonths(60));
                } elseif ($category === 'remaja') {
                    // Age 61-216 months
                    $q->where('date_of_birth', '<', Carbon::parse($today)->subMonths(60))
                        ->where('date_of_birth', '>=', Carbon::parse($today)->subMonths(216));
                } elseif ($category === 'dewasa') {
                    // Age > 216 months
                    $q->where('date_of_birth', '<', Carbon::parse($today)->subMonths(216));
                }
            });
        }

        // Gender filter
        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        // Trashed filter
        if (isset($filters['trashed']) && $filters['trashed']) {
            $query->onlyTrashed();
        } else {
            // Default to not showing trashed unless explicitly asked, or keep withTrashed?
            // Actually, existing code used withTrashed() unconditionally. 
            // If they want ONLY trashed:
            if (isset($filters['only_trashed']) && $filters['only_trashed']) {
                $query->onlyTrashed();
            }
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($perPage);
    }

    /**
     * Create a new subject with duplicate check.
     */
    public function createSubject(array $data): Subject
    {
        // Generate normalized_name if not provided
        $normalizedName = $data['normalized_name'] ?? Subject::normalizeName($data['name']);
        $data['normalized_name'] = $normalizedName;

        // Add user_id if not provided
        $data['user_id'] = $data['user_id'] ?? auth()->id();

        // Check for duplicate
        $existing = $this->findDuplicate($normalizedName, $data['date_of_birth']);

        if ($existing) {
            throw new DuplicateSubjectException($existing);
        }

        return Subject::create($data);
    }


    /**
     * Update a subject.
     */
    public function updateSubject(Subject $subject, array $data): Subject
    {
        // Check for duplicate if name or date_of_birth changed
        if (isset($data['normalized_name']) || isset($data['date_of_birth'])) {
            $normalizedName = $data['normalized_name'] ?? $subject->normalized_name;
            $dateOfBirth = $data['date_of_birth'] ?? $subject->date_of_birth;

            $existing = $this->findDuplicate($normalizedName, $dateOfBirth, $subject->id);

            if ($existing) {
                throw new DuplicateSubjectException($existing);
            }
        }

        $subject->update($data);
        return $subject->fresh();
    }

    /**
     * Find duplicate subject by normalized name and date of birth.
     */
    public function findDuplicate(string $normalizedName, mixed $dateOfBirth, ?int $excludeId = null): ?Subject
    {
        $query = Subject::where('normalized_name', $normalizedName)
            ->where('date_of_birth', $dateOfBirth);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->first();
    }

    /**
     * Calculate age in months from date of birth.
     * Per 08_calculation_logic.md §2.1
     */
    public function calculateAgeInMonths(mixed $dateOfBirth, ?string $measurementDate = null): int
    {
        $birthDate = Carbon::parse($dateOfBirth);
        $measurementDate = $measurementDate ? Carbon::parse($measurementDate) : now();

        $ageInMonths = ($measurementDate->year - $birthDate->year) * 12
            + ($measurementDate->month - $birthDate->month);

        // Adjust if measurement day is before birth day
        if ($measurementDate->day < $birthDate->day) {
            $ageInMonths--;
        }

        return max(0, $ageInMonths);
    }

    /**
     * Calculate age in years from date of birth.
     */
    public function calculateAgeInYears(mixed $dateOfBirth, ?string $measurementDate = null): int
    {
        $birthDate = Carbon::parse($dateOfBirth);
        $measurementDate = $measurementDate ? Carbon::parse($measurementDate) : now();

        $ageInYears = $measurementDate->year - $birthDate->year;

        // Adjust if measurement month/day is before birth month/day
        if (
            $measurementDate->month < $birthDate->month ||
            ($measurementDate->month === $birthDate->month && $measurementDate->day < $birthDate->day)
        ) {
            $ageInYears--;
        }

        return max(0, $ageInYears);
    }

    /**
     * Determine category based on age in months.
     * Per 08_calculation_logic.md §2.2
     */
    public function determineCategory(int $ageInMonths, bool $isPregnant = false): string
    {
        if ($isPregnant) {
            return 'dewasa'; // Ibu Hamil calculations are handled inside CalculateDewasaAction
        }

        if ($ageInMonths <= 60) {
            return 'balita';
        } elseif ($ageInMonths <= 216) {
            return 'remaja';
        }

        return 'dewasa';
    }
}
