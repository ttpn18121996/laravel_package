<?php

namespace PhuongNam\UserAndPermission\Helpers;

use Illuminate\Support\Facades\Schema;

trait NModelTrait
{
    /**
     * Lấy danh sách và chia trang
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  array  $filter
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginate($builder, array $filter = [])
    {
        $perpage = $filter['per_page'] ?? 10;
        $page = $filter['page'] ?? 1;
        $pageName = $filter['pageName'] ?? 'page';

        if ($perpage <= 0) {
            return $builder->get();
        }

        return $builder->paginate($perpage, $columns = ['*'], $pageName, $page);
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

        if (! is_null($filter)) {
            foreach ($filter as $column => $value) {
                if (strpos($column, '@') !== false) {
                    $column = str_replace('@', '.', $column);
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
                    } elseif (strpos($column, '@') !== false) {
                        $column = str_replace('@', '.', $column);
                        $builder->where("{$column}", 'like', "%{$value}%");
                        break;
                    }
                }
            }
        }

        return $builder;
    }

    /**
     * Lấy danh sách và kiểm tra giá trị đã chọn cho <select>
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $config (list, columns, wheres)
     * @return \Illuminate\Support\Collection
     */
    public function getListAndCheckSelected(\Illuminate\Database\Eloquent\Model $model, array $config = [])
    {
        $query = $model;

        if (isset($config['wheres'])) {
            $query = $query->where($config['wheres']);
        }

        return $query->get($config['columns'] ?? ['*'])->map(function ($item) use ($config) {
            $item->selected = in_array($item->id, $config['list'] ?? []);
            return $item;
        });
    }
}
