<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Support\IframeDetector;

class BaseIframeModel extends Model
{
    /**
     * Dynamically switch database connection
     */
    public function getConnectionName()
    {
        $source = IframeDetector::source();

        if ($source) {
            $connection = config("iframe.connections.$source");

            if ($connection) {
                return $connection;
            }
        }

        return parent::getConnectionName();
    }

    /**
     * Dynamically switch table per model per source
     */
    public function getTable()
    {
        $source = IframeDetector::source();

        if ($source) {
            $table = config("iframe.tables.$source." . static::class);

            if ($table) {
                return $table;
            }
        }

        return parent::getTable();
    }

}
