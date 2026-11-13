<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RapidShipmentRecord extends Model
{
    protected $table = "vw_shipment_report";
    protected $connection = "mysql_rapid";
    // protected $connection = "mysql_rapid_live";

    // // Switch DB + table when accessed via iframe
    // public function getConnectionName(){
    //     return session('is_iframe') ? 'mysql_iframe_preshipment' : $this->connection;
    // }

    // public function getTable(){
    //     return session('is_iframe') ? 'vw_shipment_report' : $this->table;
    // }

    // // Iframe-aware connection
    // public function getConnectionName(){
    //     $source = IframeDetector::source();

    //     if ($source && config("iframe.connections.$source")){
    //         return config("iframe.connections.$source");
    //     }

    //     return $this->connection;
    // }

    // // Iframe-aware table
    // public function getTable(){
    //     $source = IframeDetector::source();

    //     if ($source && config("iframe.tables.$source")){
    //         return config("iframe.tables.$source");
    //     }

    //     return $this->table;
    // }
}
