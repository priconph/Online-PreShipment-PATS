<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;



// use App\Model\Preshipment;
use App\Model\PreshipmentList;
use App\Model\RapidPreshipment;
use App\Model\RapidPreshipmentList;
use App\Model\PreshipmentApproving;
use App\Model\UserAccess;
use App\Model\RapidStamping;

use App\Support\IframeDetector;

use DataTables;
use Mail;

class QCPreshipmentController extends Controller
{

    public function get_Preshipment_QC(){
        // $preshipment = RapidPreshipment::whereIN('rapidx_QCChecking',['1','2'])
        $preshipment = RapidPreshipment::whereIN('rapidx_QCChecking',['1'])
        ->orderBy('id', 'DESC')
        ->where('logdel', 0)
        ->get();

        return DataTables::of($preshipment)
        ->addColumn('status', function($preshipment){
            $result = "<center>";
            if($preshipment->rapidx_QCChecking == 1){

                $result .='<span class="badge badge-info">For QC Checking</span>';
            }
            else{
                $result .='<span class="badge badge-success">Checked</span>';
            }


            $result .='</center>';

            return $result;
        })
        ->addColumn('action', function($preshipment) {

            $rapidx_preshipment = PreshipmentApproving::where_preshipment($preshipment->id)
            // where('fk_preshipment_id',$preshipment->id)
            ->where('logdel', 0)
            ->first();


            $result = "<center>";

            $result .= '<button class="btn btn-primary btn-sm btn-openshipment mr-1"  data-toggle="modal" data-target="#modalViewQCChecksheets" checksheet-id="'.$preshipment->Packing_List_CtrlNo.'"><i class="fas fa-eye"></i></button>';
            if($rapidx_preshipment != null){
                $result .= '<div class="btn-group">
                <button type="button" class="btn btn-secondary mr-1 dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Exports">
                <i class="fas fa-lg fa-file-download"></i>
                </button>';
                    $result .= '<div class="dropdown-menu dropdown-menu-right">'; // dropdown-menu start

                    $result .='<a class="dropdown-item text-center" href="export_excel/'.$rapidx_preshipment->id.'" target="_blank">Export Excel</a>';
                    $result .='<a class="dropdown-item text-center" href="pdf_export/'.$rapidx_preshipment->id.'" target="_blank">Export PDF</a>';

                    $result .= '</div>'; // dropdown-menu end
                $result .= '</div>';
            }


            $result .="</center>";
            return $result;
        })
        ->rawColumns(['action','status'])
        ->make(true);
    }

    public function getpreshipmentbyCtrlNo_QC(Request $request){

        $preshipment = RapidPreshipment::where('Packing_List_CtrlNo',$request->preshipment_ctrl_id)
        ->get();


        $preshipment_list = PreshipmentList::where('fkControlNo',$preshipment[0]['Packing_List_CtrlNo'])
        ->where('logdel', 0)
        ->get();

        // return $preshipment_list;


        if(count($preshipment) > 0){
            return response()->json(['response' => 1, 'preshipment' => $preshipment, 'preshipmentList' => $preshipment_list]);
        }
        else{
            return response()->json(['response' => 0]);

        }
    }

    public function disapprove_list_QC(Request $request){
        date_default_timezone_set('Asia/Manila');
        $data = $request->all();

                RapidPreshipment::where('Packing_List_CtrlNo', $request->packingCtrlNo)
                ->update([ // The update method expects an array of column and value pairs representing the columns that should be updated.
                    'rapidx_QCChecking' => '3',
                    'remarks' => $request->remarks,
                    'has_invalid' => '0'
                ]);

                // return response()->json(['result' => "1"]);

                $get_to_emails = UserAccess::where('department', 'material handler')
                ->where('logdel', 0)
                ->get();

                $get_cc_emails = UserAccess::where('department', 'inspector')
                ->where('logdel', 0)
                ->get();

                $to_email = array();
                $cc_email = array();

                foreach($get_to_emails as $get_to_email){
                    $to_email[] = $get_to_email->email;
                }
                foreach($get_cc_emails as $get_cc_email){
                    $cc_email[] = $get_cc_email->email;
                }

                // // // $to_email = ['cpagtalunan@pricon.ph'];
                // // // $cc_email = ['cpagtalunan@pricon.ph'];
                $preshipment_details = RapidPreshipment::where('Packing_List_CtrlNo', $request->packingCtrlNo)
                ->where('logdel', 0)
                ->first();

                $packing_ctrl_num = $preshipment_details->Destination."-".$request->packingCtrlNo;

                $data = array('data' => $preshipment_details,'remarks' => $request->remarks, 'position' => "QC");


                Mail::send('mail.disapprove_mail', $data, function($message) use ($to_email,$cc_email,$packing_ctrl_num){
                    $message->to($to_email);
                    $message->cc($cc_email);
                    // $message->bcc('cpagtalunan@pricon.ph');
                    $message->bcc('mrronquez@pricon.ph');
                    $message->bcc('cpagtalunan@pricon.ph');

                    $message->subject("Online Preshipment QC Disapprove-".$packing_ctrl_num);
                });

                // if (Mail::failures()) {
                    // return response()->json(['result' => 0]);
                // }
                // else{
                    return response()->json(['result' => 1]);
                // }


            // }
            // catch(\Exception $e) {

            //     return response()->json(['result' => "0", 'tryCatchError' => $e->getMessage()]);
            // }
    }

    public function approve_list_QC(Request $request){
        date_default_timezone_set('Asia/Manila');
        session_start();

        $getList= RapidPreshipmentList::where('fkControlNo',$request->packingCtrlNo)
        ->where('logdel', 0)
        ->get();

        $getPackinglist = RapidPreshipment::where('Packing_List_CtrlNo',$request->packingCtrlNo)
        ->where('logdel', 0)
        ->get();

        // will check if the preshipment is an internal shipment of external shipment
        $destination_check = array(
            "Burn-in Sockets",
            "Grinding",
            "Burn-in Memory Sockets",
            "Burn-in Other Sockets",
            "Grinding Multi-Spindle",
            "Grinding Conventional",
            "Flexicon & Connectors",
            "FUS/FRS/FMS Connector",
            "Card Connector",
            "TC/DC Connectors",
            "Flexicon & TC/DC Connectors",
            "CN171 Connector",
            "Battery Connector - Stamping",
            "Flexicon & Others - Stamping"
        );

        // $destination_check = array("Burn-in Sockets","Grinding","Flexicon & Connectors","FUS/FRS/FMS Connector","Card Connector","TC/DC Connector");// will check if the preshipment is an internal shipment of external shipment

        /*
            For checked_by in DB.
            to be reflected on preshipment export "checked by" column.
            static based on rapidx id.
            // 107 - Milly Laural - PPS CN
            // 134 - Henry D. De Guzman - PPS TS & STAMPING
            // 112 - Nolasco Mendoza - PPS GRINDING
            // 29 - Jo Cahilig - PPS GRINDING BURNIN
        */
        $pps_ts = array('CARLO','CHRISTIAN','CHE','ALLEN','KYLE'); // 134 - Henry D. De Guzman
        $pps_cn = array('ENRICO','MHALOU', 'INTON'); // 107 - Milly Laural
        $pps_grinding = array('MAE ANN','JAYVEE','LUZ'); // 112 - Nolasco Mendoza
        $pps_grinding_burn_in = array('KATE'); // 29 - Jo Cahilig
        $pps_stamping = array('CHAN','MARK','ALBIE','JAYSON','ADRIAN','ARNEL','KERWIN'); // 134 - Henry D. De Guzman
        $checked_by = "";

        if(in_array(strtoupper($getList[0]->PackedBy),$pps_ts)){
            // return "ts";
            $checked_by = "134";
        }
        else if(in_array(strtoupper($getList[0]->PackedBy),$pps_cn)){
            // return "cn";
            $checked_by = "107";
        }
        else if(in_array(strtoupper($getList[0]->PackedBy),$pps_grinding)){
            // return "pps_grinding";
            $checked_by = "112";
        }
        else if(in_array(strtoupper($getList[0]->PackedBy),$pps_grinding_burn_in)){
            // return "pps_grinding_burn_in";
            $checked_by = "29";
        }
        else if(in_array(strtoupper($getList[0]->PackedBy),$pps_stamping)){
            // return "pps_stamping";
            $checked_by = "134";
        }

        $check_double_data = PreshipmentApproving::where_preshipment($getPackinglist[0]->id)
        // where('fk_preshipment_id', $getPackinglist[0]->id)
        ->where('logdel', 0)
        ->get();

        if(count($check_double_data) > 0){
            PreshipmentApproving::where_preshipment($getPackinglist[0]->id)
            // where('fk_preshipment_id', $getPackinglist[0]->id)
            ->where('logdel', 0)
            ->update([
                'logdel' => 1
            ]);
        }

        // check if preshipment is external or internal
        if(in_array($getPackinglist[0]->Destination, $destination_check)){
            // Internal Shipment
            $data = [
                'fk_preshipment_id'    => null,
                'ppdcn_preship_id'     => null,
                'ppdf3_preship_id'     => null,
                'qc_checker'           => $_SESSION['rapidx_user_id'],
                'qc_checker_date_time' => now(),
                'checked_by'           => $checked_by,
                'status'               => 0,
                'created_at'           => now(),
            ];

            $foreignKeyColumn = PreshipmentApproving::resolveForeignKeyColumn();

            // Set only the correct column
            if ($foreignKeyColumn) {
                $data[$foreignKeyColumn] = $getPackinglist[0]->id;
            }

            $preshipment_approving_id = PreshipmentApproving::insertGetId($data);

            // old code before dynamic foreign key handling
            // $preshipment_approving_id = PreshipmentApproving::insertGetId([
            //     'fk_preshipment_id'     => $fk_preshipment_id,
            //     'ppdcn_preship_id'      => $ppdcn_preship_id,
            //     'qc_checker'            => $_SESSION['rapidx_user_id'],
            //     'qc_checker_date_time'  => NOW(),
            //     'checked_by'            => $checked_by,
            //     'status'                => 0,
            //     'created_at'            => NOW()
            // ]);

            RapidPreshipment::where('Packing_List_CtrlNo', $request->packingCtrlNo)
            ->update([ // The update method expects an array of column and value pairs representing the columns that should be updated.
                'rapidx_QCChecking' => '2',
                'has_invalid' => '0'
            ]);

            $preshipment_details = PreshipmentApproving::with([
                'preshipment'
            ])
            ->where('id', $preshipment_approving_id)
            ->where('logdel', 0)
            ->first();

            $data = array('data' => $preshipment_details);
            $packing_ctrl_num = $preshipment_details->Packing_List_CtrlNo;

            $get_to_emails = UserAccess::where('department', 'PPS WHSE')
            ->where('logdel', 0)
            ->get();

            $get_cc_emails = UserAccess::where('department', 'inspector')
            ->where('logdel', 0)
            ->get();

            $to_email = array();
            $cc_email = array();

            foreach($get_to_emails as $get_to_email){
                $to_email[] = $get_to_email->email;
            }

            foreach($get_cc_emails as $get_cc_email){
                $cc_email[] = $get_cc_email->email;
            }

            // $to_email = ['cpagtalunan@pricon.ph'];
            // $cc_email = ['cpagtalunan@pricon.ph'];

            Mail::send('mail.inpector_notification', $data, function($message) use ($to_email,$cc_email,$packing_ctrl_num){
                $message->to($to_email);
                $message->cc($cc_email);
                // $message->bcc('cpagtalunan@pricon.ph');
                $message->bcc('mrronquez@pricon.ph');
                $message->bcc('cpagtalunan@pricon.ph');

                $message->subject("Online Preshipment for PPS-WHSE Acceptance-".$packing_ctrl_num);
            });

            return response()->json(['result' => 1]);
        }else{
            //external
            RapidPreshipment::where('Packing_List_CtrlNo', $request->packingCtrlNo)
            ->update([ // The update method expects an array of column and value pairs representing the columns that should be updated.
                'rapidx_QCChecking' => '2'
            ]);

            //Clark code 02/16/2026
            $data = [
                'fk_preshipment_id'    => null,
                'ppdcn_preship_id'     => null,
                'ppdf3_preship_id'     => null,
                'qc_checker'           => $_SESSION['rapidx_user_id'],
                'qc_checker_date_time' => now(),
                'checked_by'           => $checked_by,
                'status'               => 0,
                'created_at'           => now(),
            ];

            $foreignKeyColumn = PreshipmentApproving::resolveForeignKeyColumn();

            // Set only the correct column
            if ($foreignKeyColumn) {
                $data[$foreignKeyColumn] = $getPackinglist[0]->id;
            }

            $preshipment_approving_id = PreshipmentApproving::insertGetId($data);

            // old code before dynamic foreign key handling
            // $preshipment_approving_id = PreshipmentApproving::insertGetId([
            //     'fk_preshipment_id'     => $fk_preshipment_id,
            //     'ppdcn_preship_id'      => $ppdcn_preship_id,
            //     'qc_checker'            => $_SESSION['rapidx_user_id'],
            //     'qc_checker_date_time'  => NOW(),
            //     'checked_by'            => $checked_by,
            //     'status'                => 5,
            //     'created_at'            => NOW()
            // ]);

            $preshipment_details = PreshipmentApproving::with([
                'preshipment'
            ])
            ->where('id', $preshipment_approving_id)
            ->where('logdel', 0)
            ->first();

            $data = array('data' => $preshipment_details);

            $packing_ctrl_num = $preshipment_details->preshipmentData->Destination."-".$preshipment_details->preshipmentData->Packing_List_CtrlNo;

            $get_to_emails = UserAccess::where('department', 'material handler')
            ->where('logdel', 0)
            ->get();

            $get_cc_emails = UserAccess::where('department', 'inspector')
            ->where('logdel', 0)
            ->get();

            $to_email = array();
            $cc_email = array();

            foreach($get_to_emails as $get_to_email){
                $to_email[] = $get_to_email->email;
            }
            foreach($get_cc_emails as $get_cc_email){
                $cc_email[] = $get_cc_email->email;
            }

            // $to_email = ['cpagtalunan@pricon.ph'];
            // $cc_email = ['cpagtalunan@pricon.ph'];

            Mail::send('mail.inpector_notification_ext', $data, function($message) use ($to_email,$cc_email,$packing_ctrl_num){
                $message->to($to_email);
                $message->cc($cc_email);
                // $message->bcc('cpagtalunan@pricon.ph');
                $message->bcc('mrronquez@pricon.ph');
                $message->bcc('cpagtalunan@pricon.ph');
                $message->subject("Online Preshipment Inspector Approval-".$packing_ctrl_num);
            });

            // if (Mail::failures()) {
            //     return response()->json(['result' => 0]);
            // }
            // else{
                return response()->json(['result' => 1]);
            // }
            // return response()->json(['result' => 1,]);
        }
    }

    public function insert_preshimentlist_from_qc_qr_checking(Request $request){
        $data = $request->all();


        PreshipmentList::insert([
            'fkControlNo' => $request->preshipment_ctrl_no,
            'PONo' => $request->po_num,
            'Partscode' => $request->partcode,
            'DeviceName' => $request->device_name,
            'LotNo' => $request->lot_no,
            'Qty' => $request->qty,
            'PackageCategory' => $request->package_category,
            'PackageQty' => $request->package_qty
        ]);

    }




    //change 07/14/2022
    public function get_Preshipment_done(Request $request){
        // $preshipment = RapidPreshipment::with([
        //     'rapidx_preshipment_app_details'
        // ])
        // ->where('rapidx_QCChecking',2)
        // ->orderBy('id', 'DESC')
        // ->where('logdel', 0)
        // ->get();
        $preshipment = DB::connection('mysql_rapid')
        ->table('tbl_PreShipment')
        ->whereBetween('Date', [$request->dateFrom, $request->dateTo])
        ->where('rapidx_QCChecking', 2)
        ->where('logdel', 0)
        ->limit(30)
        ->get();

        // $preshipment = DB::connection('mysql_rapid', 'mysql')
        // ->select("
        //     SELECT * FROM tbl_PreShipment INNER JOIN preshipment_approvings ON tbl_PreShipment.id = preshipment_approvings.fk_preshipment_id
        //     WHERE `rapidx_QCChecking` = '2' AND `logdel` = '0' ORDER BY `id` DESC
        // ");


        return DataTables::of($preshipment)
        ->addColumn('status', function($preshipment){
            $result = "<center>";
            // if($preshipment->rapidx_QCChecking == 1){

            //     $result .='<span class="badge badge-info">For QC Checking</span>';
            // }
            // else{
                $result .='<span class="badge badge-success">Checked</span>';
            // }


            $result .='</center>';

            return $result;
        })
        ->addColumn('action', function($preshipment) {
            $result = "";

            $rapidx_preshipment = PreshipmentApproving::where_preshipment($preshipment->id)
            // where('fk_preshipment_id',$preshipment->id)
            ->where('logdel', 0)
            ->first();

            // $rapidx_preshipment = DB::connection('mysql')
            // ->table('preshipment_approvings')
            // ->where('fk_preshipment_id', $preshipment->id)
            // ->where('logdel', 0)
            // ->first();

            $result .= "<center>";

            $result .= '<button class="btn btn-primary btn-sm btn-openshipment mr-1"  data-toggle="modal" data-target="#modalViewQCChecksheets" checksheet-id="'.$preshipment->Packing_List_CtrlNo.'"><i class="fas fa-eye"></i></button>';
            if($rapidx_preshipment != null){
                $result .= '<div class="btn-group">
                <button type="button" class="btn btn-secondary mr-1 dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Exports">
                <i class="fas fa-lg fa-file-download"></i>
                </button>';
                    $result .= '<div class="dropdown-menu dropdown-menu-right">'; // dropdown-menu start

                    $result .='<a class="dropdown-item text-center" href="export_excel/'.$rapidx_preshipment->id.'" target="_blank">Export Excel</a>';
                    $result .='<a class="dropdown-item text-center" href="pdf_export/'.$rapidx_preshipment->id.'" target="_blank">Export PDF</a>';

                    $result .= '</div>'; // dropdown-menu end
                $result .= '</div>';
            }

            $result .="</center>";
            return $result;
        })
        ->rawColumns(['action','status'])
        ->make(true);
    }

}
