<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Iframe Source → Database Connection Mapping
    |--------------------------------------------------------------------------
    */

    'connections' => [
        'pats_ppd_cn' => 'iframe_patsppdcn',
        'pats_ppd_f3' => 'iframe_patsppdf3',
    ],

    /*
    |--------------------------------------------------------------------------
    | Iframe Source → Table Mapping
    |--------------------------------------------------------------------------
    */

    // 'tables' => [
    //     'pats_ppd_cn' => 'db_pps_preshipments',
    //     'pats_ppd_f3' => 'db_pps_preshipments',
    // ],

    'tables' => [
        'pats_ppd_cn' => [
            \App\Model\RapidPreshipment::class => 'db_pps_preshipments',
            \App\Model\RapidPreshipmentList::class => 'db_pps_preship_transactions',
            \App\Model\RapidShipmentInvoice::class => 'shipments',
            \App\Model\RapidShipmentRecord::class => 'vw_shipment_report',
        ],

        'pats_ppd_f3' => [
            \App\Model\RapidPreshipment::class => 'db_pps_preshipments',
            \App\Model\RapidPreshipmentList::class => 'db_pps_preship_transactions',
            \App\Model\RapidShipmentInvoice::class => 'shipments',
            \App\Model\RapidShipmentRecord::class => 'vw_shipment_report',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Iframe Foreign Key → Relationship Mapping
    |--------------------------------------------------------------------------
    */

    // 'foreign_keys' => [
    //     'pats_ppd_cn' => 'ppdcn_preship_id',
    //     'pats_ppd_f3' => 'ppdf3_preship_id',
    // ],

    'foreign_key_columns' => [
        'legacy'      => 'fk_preshipment_id',
        'pats_ppd_cn' => 'ppdcn_preship_id',
        'pats_ppd_f3' => 'ppdf3_preship_id',
    ],

];
