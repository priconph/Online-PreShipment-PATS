<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
// use App\Model\Preshipment;
use App\Model\UserAccess;
use App\Model\RapidxUser;
use App\Model\RapidPreshipment;
use Illuminate\Database\Eloquent\Builder;
use App\Support\IframeDetector;

class PreshipmentApproving extends BaseTenantModel
{
    protected $table = "preshipment_approvings";
    protected $connection = "mysql";

    public function preshipment(){
        $fk = $this->resolveForeignKey('preshipment', 'fk_preshipment_id');
        return $this->belongsTo(RapidPreshipment::class, $fk, 'id');
    }

    protected function resolveForeignKey($relationName, $defaultColumn){
        return self::resolveForeignKeyColumn() ?? $defaultColumn;
    }

    public static function resolveForeignKeyColumn(){
        $source = IframeDetector::source() ?? 'legacy';
        return config("iframe.foreign_key_columns.$source") ?? 'fk_preshipment_id';
    }

    // public function Preshipment_for_approval(){
    //     return $this->hasOne(Preshipment::class, 'id', 'fk_preshipment_id');
    // }

    // // Normal system relationship
    // public function preshipment(){
    //     return $this->hasOne(RapidPreshipment::class, 'id', 'fk_preshipment_id');
    // }

    // // Iframe system
    // public function iframePreshipment(){
    //     return $this->hasOne(RapidPreshipment::class, 'id', 'ppdcn_preship_id');
    // }

    // /**
    //  * SMART accessor with fallback
    //  */
    // public function getPreshipmentDataAttribute(){
    //     if (session('is_iframe')) {
    //         // If iframe column exists, use it
    //         if (!empty($this->ppdcn_preship_id)) {
    //             return $this->iframePreshipment;
    //         }

    //         // Fallback for old records
    //         return $this->preshipment;
    //     }
    //     return $this->preshipment;
    // }

    public function checked_by_details(){
        return $this->hasOne(UserAccess::class, 'rapidx_id', 'checked_by');
    }

    public function checked_by_details_from_rapidx_user(){
        return $this->hasOne(RapidxUser::class, 'id', 'checked_by');
    }

    public function qc_approver_details(){
        return $this->hasOne(UserAccess::class, 'rapidx_id', 'qc_checker');
    }

    public function from_user_details(){
        return $this->hasOne(UserAccess::class, 'rapidx_id', 'from_whse_noter');
    }

    public function to_whse_noter_details(){
        return $this->hasOne(UserAccess::class, 'rapidx_id', 'to_whse_noter');
    }

    public function whse_uploader_details(){
        return $this->hasOne(UserAccess::class, 'rapidx_id', 'whse_uploader');
    }

    public function whse_superior_details(){
        return $this->hasOne(UserAccess::class, 'rapidx_id', 'whse_superior_noter');
    }
}
