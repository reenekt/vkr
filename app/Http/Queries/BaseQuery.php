<?php

namespace App\Http\Queries;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Validation\ValidationException;

/**
 * @mixin Builder
 */
abstract class BaseQuery
{
    use ForwardsCalls;

    public Builder $baseQuery;

    public function __construct(public readonly Request $request)
    {
        $this->configureBaseQuery();
        $this->configureQuery();
    }

    abstract protected function configureBaseQuery(): void;

    protected function configureQuery(): void
    {
        if ($this->request->has('filters')) {
            $this->configureFilters($this->request->get('filters'));
        }
    }

    protected function configureFilters(array $filters): void
    {
        $allowedFilters = $this->allowedFilters();

        foreach ($filters as $filterKey => $filterValue) {
            if (!array_key_exists($filterKey, $allowedFilters)) {
                throw ValidationException::withMessages(['query.filters' => "Фильтр $filterKey не доступен"]);
            }

            $filterType = $allowedFilters[$filterKey];

            if ($filterType === 'exact') {
                $this->baseQuery->where($filterKey, $filterValue);
            } elseif ($filterType === 'datetime_range') {
                if (!is_array($filterValue) || count($filterValue) !== 2) {
                    throw ValidationException::withMessages(['query.filters' => "Значение фильтра $filterKey должно быть массивом с двумя элементами (начало и конец периода)"]);
                }

                if (!empty($filterValue[0])) {
                    $this->baseQuery->whereDate($filterKey, '>=', $filterValue[0]);
                }

                if (!empty($filterValue[1])) {
                    $this->baseQuery->whereDate($filterKey, '<=', $filterValue[1]);
                }
            }
        }
    }

    abstract public function allowedFilters(): array;

    public function __call(string $method, array $parameters)
    {
        return $this->forwardCallTo($this->baseQuery, $method, $parameters);
    }
}
