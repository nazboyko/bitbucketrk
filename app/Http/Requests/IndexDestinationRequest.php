<?php

namespace App\Http\Requests;

use App\Enums\DestinationSort;
use App\Enums\SortDirection;
use App\Models\Destination;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexDestinationRequest extends FormRequest
{
    private const int MAX_PER_PAGE = 100;

    /**
     * @return array<string, list<ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:'.Destination::SEARCH_MAX_LENGTH],
            'sort' => [Rule::enum(DestinationSort::class)],
            'direction' => [Rule::enum(SortDirection::class)],
            'page' => ['integer', 'min:1'],
            'per_page' => ['integer', 'between:1,'.self::MAX_PER_PAGE],
        ];
    }

    public function search(): string
    {
        return $this->string('search')->toString();
    }

    public function sort(): DestinationSort
    {
        return $this->enum('sort', DestinationSort::class, DestinationSort::Name);
    }

    public function direction(): SortDirection
    {
        return $this->enum('direction', SortDirection::class, SortDirection::Ascending);
    }

    /**
     * Null leaves the page size to the model default.
     */
    public function perPage(): ?int
    {
        return $this->integer('per_page') ?: null;
    }
}
