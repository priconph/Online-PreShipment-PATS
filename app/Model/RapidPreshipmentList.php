<?php

namespace App\Model;

use App\Model\RapidDieset;
use Illuminate\Database\Eloquent\Model;

class RapidPreshipmentList extends BaseIframeModel{
    protected $table = "tbl_PreShipmentTransaction";
    protected $connection = "mysql_rapid";

    public function dieset_info(){
        return $this->hasOne(RapidDieset::class, 'R3Code', 'Partscode');
    }

//     // Switch DB + table when accessed via iframe
//     public function getConnectionName(){
//         return session('is_iframe') ? 'mysql_iframe_preshipment' : $this->connection;
//     }

//     public function getTable(){
//         return session('is_iframe') ? 'db_pps_preship_transactions' : $this->table;
//     }
}
