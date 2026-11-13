<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RapidShipmentInvoice extends BaseIframeModel
{
    protected $table = "tbl_ShipmentInvoice";
    // protected $connection = "mysql_rapid_live";
    protected $connection = "mysql_rapid";

    // // Switch DB + table when accessed via iframe
    // public function getConnectionName(){
    //     return session('is_iframe') ? 'mysql_iframe_preshipment' : $this->connection;
    // }

    // public function getTable(){
    //     return session('is_iframe') ? 'shipments' : $this->table;
    // }
}
