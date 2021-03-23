<?php

namespace PhuongNam\UserAndPermission\Helpers\Traits;

trait HasSortAndSearch
{
    /**
     * Sắp xếp.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  array  $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function sortBuilder($builder, array $filter = [])
    {
        $filter = array_filter($filter, function ($value) {
            return !is_null($value) && $value !== '';
        });
        $alias = $this->table;
        $nameTable = $this->table;

        if (strpos($this->table, ' as ') !== false) {
            $arrName = explode(' as ', $this->table);
            $nameTable = $arrName[0];
            $alias = $arrName[1];
        }

        $columnNames = Schema::getColumnListing($nameTable);

        if ($filter != null) {
            foreach ($filter as $column => $value) {
                if (strpos($column, 'sort_') !== false) {
                    $column = str_replace('sort_', '', $column);
                    if (in_array($column, $columnNames)) {
                        $builder->orderBy("{$alias}.{$column}", $value);
                        break;
                    } elseif (strpos($column, '%') !== false) {
                        $column = str_replace('%', '.', $column);
                        $builder->where("{$column}", 'like', "%{$value}%");
                        break;
                    }
                }
            }
        }

        return $builder;
    }

    /**
     * Tìm kiếm.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  array  $filter
     * @param  array  $equalColumns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function searchBuilder($builder, array $filter = [], array $equalColumns = [])
    {
        $filter = array_filter($filter, function ($value) {
            return ! is_null($value) && $value !== '';
        });
        $alias = $this->table;
        $nameTable = $this->table;
        if (strpos($this->table, ' as ') !== false) {
            $arrName = explode(' as ', $this->table);
            $nameTable = $arrName[0];
            $alias = $arrName[1];
        }

        $columnNames = Schema::getColumnListing($nameTable);

        if (! is_null($filter)) {
            foreach ($filter as $column => $value) {
                if (strpos($column, '%') !== false) {
                    $column = str_replace('%', '.', $column);
                    $builder->where("{$column}", 'like', "%{$value}%");
                } elseif (in_array($column, $columnNames)) {
                    if (in_array($column, $equalColumns)) {
                        $builder->where("{$alias}.{$column}", $value);
                    } else {
                        $builder->where("{$alias}.{$column}", 'like', "%{$value}%");
                    }
                }
            }
        }

        return $builder;
    }
}
