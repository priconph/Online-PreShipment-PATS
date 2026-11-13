<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
// use App\Support\IframeDetector;

abstract class BaseTenantModel extends Model{
    public function scopeForCurrentSource($query)
    {
        if (method_exists(static::class, 'resolveForeignKeyColumn')) {
            $column = static::resolveForeignKeyColumn();
            $query->whereNotNull($column)->orderBy($column, 'DESC');
        }

        return $query;
    }

    public function scopeWhereNotNullFkColumn($query)
    {
        if (method_exists(static::class, 'resolveForeignKeyColumn')) {
            $column = static::resolveForeignKeyColumn();
            $query->whereNotNull($column);
        }
        return $query;
    }

    public function scopeOrderByFkColumn($query)
    {
        if (method_exists(static::class, 'resolveForeignKeyColumn')) {
            $column = static::resolveForeignKeyColumn();
            $query->orderBy($column, 'DESC');
        }
        return $query;
    }

    public function scopeWherePreshipment($query, $id)
    {
        $column = static::resolveForeignKeyColumn();
        return $query->where($column, $id);
    }

    public static function fk_column_filter()
    {
        return static::query()->forCurrentSource();
    }

    public static function where_not_null_fk_col()
    {
        return static::query()->whereNotNullFkColumn();
    }

    public static function where_preshipment($id)
    {
        return static::query()->wherePreshipment($id);
    }

    public static function order_by_fk_col()
    {
        return static::query()->orderByFkColumn();
    }
}
