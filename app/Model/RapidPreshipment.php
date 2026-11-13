<?php

namespace App\Model;

// use App\Model\BaseIframeModel;
// use App\Support\IframeResolver;
use App\Support\IframeDetector;
use App\Model\PreshipmentApproving;
use App\Model\RapidShipmentInvoice;
use Illuminate\Database\Eloquent\Model;

class RapidPreshipment extends BaseIframeModel{

    protected $table = "tbl_PreShipment";
    protected $connection = "mysql_rapid";
    public $timestamps = false;

    // // Iframe-aware connection
    // public function getConnectionName(){
    //     $source = IframeDetector::source();

    //     if ($source && config("iframe.connections.$source")) {
    //         return config("iframe.connections.$source");
    //     }

    //     return $this->connection;
    // }

    // // Iframe-aware table
    // public function getTable(){
    //     $source = IframeDetector::source();

    //     if ($source && config("iframe.tables.$source")) {
    //         return config("iframe.tables.$source");
    //     }

    //     return $this->table;
    // }

    public function rapidx_preshipment_app_details(){
        $fk = $this->resolveForeignKey('rapidx_preshipment_app_details', 'fk_preshipment_id');
        return $this->hasOne(PreshipmentApproving::class, $fk, 'id');
    }

    protected function resolveForeignKey($relationName, $defaultColumn){
        return self::resolveForeignKeyColumn() ?? $defaultColumn;
    }

    public static function resolveForeignKeyColumn(){
        $source = IframeDetector::source() ?? 'legacy';
        return config("iframe.foreign_key_columns.$source") ?? 'fk_preshipment_id';
    }

    // orig code before refactor
    // public function rapidx_preshipment_app_details(){
    //     return $this->hasOne(PreshipmentApproving::class, 'fk_preshipment_id', 'id');
    // }

    public function rapid_shipment_invoice_details(){
        return $this->hasOne(RapidShipmentInvoice::class, 'id_tbl_PreShipment', 'id');
    }

    // Switch DB + table when accessed via iframe
    // public function getConnectionName(){
    //     return session('is_iframe') ? 'mysql_iframe_preshipment' : $this->connection;
    // }

    // public function getTable(){
    //     return session('is_iframe') ? 'db_pps_preshipments' : $this->table;
    // }

    // public function getConnectionName(){
    //     return IframeResolver::isIframe() ? 'mysql_iframe_preshipment' : $this->connection;
    // }

    // public function getTable(){
    //     return IframeResolver::isIframe() ? 'db_pps_preshipments' : $this->table;
    // }

    // Switch DB per-request
    // public function getConnectionName(){
    //     $isIframe = request()->attributes->get('is_iframe', false);
    //     $source = request()->attributes->get('iframe_source', null);

    //     dd($isIframe, $source); // Will show true/false and system name

    //     return request()->attributes->get('is_iframe')
    //          ? 'mysql_iframe_preshipment'
    //         : $this->connection;
    // }

    // // Switch table per iframe source
    // public function getTable(){
    //     $source = request()->attributes->get('iframe_source');
    //     if ($source == 'systemA') return 'db_pps_preshipments';
    //     if ($source == 'systemB') return 'db_pps_preshipments';
    //     return $this->table;
    // }

    // clark comment 02/16/2026
    // Switch DB dynamically
    // public function getConnectionName()
    // {
    //     // return IframeDetector::isIframe()
    //     //     ? 'mysql_iframe_preshipment'
    //     //     : $this->connection;

    //     $source_db_conn = IframeDetector::isIframe();

    //     if ($source_db_conn == 'systemA') return 'mysql_iframe_preshipment';
    //     if ($source_db_conn == 'systemB') return 'mysql_iframe_preshipment_systemB';

    //     return $this->connection;
    // }

    // // Switch table dynamically
    // public function getTable()
    // {
    //     $source_table_conn = IframeDetector::source();

    //     if ($source_table_conn == 'systemA') return 'db_system_preshipments'; //same table name but different connection
    //     if ($source_table_conn == 'systemB') return 'db_system_preshipments'; //same table name but different connection

    //     return $this->table;
    // }
}
