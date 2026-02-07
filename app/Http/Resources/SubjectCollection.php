<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Subject collection for paginated API responses.
 * Per 07_api_specification.md §3.1
 */
class SubjectCollection extends ResourceCollection
{
    public $collects = SubjectResource::class;

    public function toArray(Request $request): array
    {
        return [
            'subjects' => $this->collection,
            'pagination' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
            ],
        ];
    }
}
