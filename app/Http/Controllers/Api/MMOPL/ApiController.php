<?php

namespace App\Http\Controllers\Api\MMOPL;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Modules\Utility\GLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public $op_type_id_issue;
    public $op_type_id_reload;
    public $reg_fee_sv;
    public $base_url;
    public $pg_id;
    public $operator_id;
    public $auth;
    public $media_type_id;

    public function __construct()
    {
        $this->op_type_id_issue = env('ISSUE');
        $this->op_type_id_reload = env('RELOAD');
        $this->pg_id = env('PHONE_PE_PG');
        $this->operator_id = env('OPERATOR_ID');
        $this->base_url = env("BASE_URL_MMOPL");
        $this->auth = env("API_SECRET");
        $this->reg_fee_sv = env('SV_REG_FEE');
        $this->media_type_id = env('MEDIA_TYPE_ID_MOBILE');
    }

    public function genSjtRjtTicket($data)
    {

        $user = DB::table('users')
            ->where('pax_id', '=', $data->pax_id)
            ->first();

        $data = '{
                "data": {
                    "fare"                      : "' . $data->total_price . '",
                    "source"                    : "' . $data->src_stn_id . '",
                    "destination"               : "' . $data->des_stn_id . '",
                    "tokenType"                 : "' . $data->pass_id . '",
                    "supportType"               : "' . $data->media_type_id . '",
                    "qrType"                    : "' . $data->product_id . '",
                    "operationTypeId"           : "' . $this->op_type_id_issue . '",
                    "operatorId"                : "' . $this->operator_id . '",
                    "operatorTransactionId"     : "' . $data->sale_or_no . '",
                    "name"                      : "' . $user->pax_name . '",
                    "email"                     : "' . $user->pax_email . '",
                    "mobile"                    : "' . $user->pax_mobile . '",
                    "activationTime"            : "' . $data->insert_date . '",
                    "trips"                     : "' . $data->unit . '"
                },
                "payment": {
                    "pass_price"                : "' . $data->total_price . '",
                    "pgId"                      : "' . $this->pg_id . '",
                    "pg_order_id"               : "' . $data->pg_txn_no . '"
                }
            }';

        GLog::info("MMOPL-REQUEST [GEN SJT / RJT]", $data);

        $response = Http::withHeaders(['Authorization' => $this->auth])
            ->withBody($data, 'application/json')
            ->post($this->base_url . '/qrcode/issueToken')
            ->collect();

        GLog::info("MMOPL_RESPONSE [GEN SJT / RJT]", $response);

        return json_decode($response);
    }

    public function getSlaveStatus($slave)
    {
        GLog::info("MMOPL_REQUEST [GET SLAVE STATUS]", $slave);

        $response = Http::withHeaders(['Authorization' => $this->auth])
            ->get($this->base_url . '/qrcode/status/' . $slave)
            ->collect();

        GLog::info("MMOPL_RESPONSE [GET SLAVE STATUS]", $response);

        return json_decode($response);
    }

    public function genStoreValuePass($data)
    {
        $user = DB::table('users')
            ->where('pax_id', '=', $data->pax_id)
            ->first();

        $data = '{
                "data": {
                    "fare"                      : "' . $data->total_price . '",
                    "tokenType"                 : "' . $data->pass_id . '",
                    "supportType"               : "' . $data->media_type_id . '",
                    "registrationFee"           : "' . $this->reg_fee_sv . '",
                    "qrType"                    : "' . $data->product_id . '",
                    "operationTypeId"           : "' . $this->op_type_id_issue . '",
                    "operatorId"                : "' . $this->operator_id . '",
                    "name"                      : "' . $user->pax_name . '",
                    "email"                     : "' . $user->pax_email . '",
                    "mobile"                    : "' . $user->pax_mobile . '",
                    "activationTime"            : "' . Carbon::now() . '",
                    "operatorTransactionId"     : "' . $data->sale_or_no . '"
                },
                "payment": {
                    "pass_price"                : "' . $data->total_price . '",
                    "pgId"                      : "' . $this->pg_id . '",
                    "pg_order_id"               : "' . $data->pg_txn_no . '"
                }
            }';

        GLog::info("MMOPL_REQUEST [GEN STORE VALUE]", $data);

        $response = Http::withHeaders(['Authorization' => $this->auth])
            ->withBody($data, 'application/json')
            ->post($this->base_url . '/qrcode/issuePass')
            ->collect();

        GLog::info("MMOPL_RESPONSE [GEN STORE VALUE]", $response);

        return json_decode($response);

    }

    public function getPassStatus($master)
    {

        GLog::info("MMOPL_REQUEST [GET PASS STATUS]", $master);

        $response = Http::withHeaders(['Authorization' => $this->auth])
            ->get($this->base_url . '/pass/bookings?masterTxnId=' . $master)
            ->collect();

        GLog::info("MMOPL_RESPONSE [GET PASS STATUS]", $response);

        return json_decode($response);
    }

    public function genTrip($data)
    {
        $user = DB::table('users')
            ->where('pax_id', '=', $data->pax_id)
            ->first();

        $data = '{
             "data": {
                    "tokenType"             :   "' . $data->total_price . '",
                    "operationTypeId"       :   "' . $this->op_type_id_issue . '",
                    "operatorId"            :   "' . $this->operator_id . '",
                    "name"                  :   "' . $user->pax_name . '",
                    "email"                 :   "' . $user->pax_email . '",
                    "mobile"                :   "' . $user->pax_mobile . '",
                    "activationTime"        :   "' . Carbon::now() . '",
                    "masterTxnId"           :   "' . $data->ms_qr_no . '",
                    "qrType"                :   "' . $data->product_id . '",
                    "tokenType"             :   "' . $data->pass_id . '"
                 }
            }';

        GLog::info("MMOPL_REQUEST [GEN TRIP]", $data);

        $response = Http::withHeaders(['Authorization' => $this->auth])
            ->withBody($data, 'application/json')
            ->post($this->base_url . '/qrcode/issueTrip')
            ->collect();

        GLog::info("MMOPL_RESPONSE [GEN TRIP]", $response);

        return json_decode($response);

    }

    public function reloadStoreValueStatus($order)
    {

        $data = '{
                "data": {
                    "fare"          : "' . $order->total_price . '",
                    "supportType"   : "' . $this->media_type_id . '",
                    "qrType"        : "' . $order->product_id . '",
                    "tokenType"     : "' . $order->pass_id . '",
                    "operatorId"    : "' . $this->operator_id . '",
                    "masterTxnId"   : "' . $order->ms_qr_no . '"
                }
            }';

        GLog::info("MMOPL_REQUEST [RELOAD STORE VALUE STATUS]", $data);

        $response = Http::withHeaders(['Authorization' => $this->auth])
            ->withBody($data, 'application/json')
            ->post($this->base_url . '/qrcode/canReloadPass')
            ->collect();

        GLog::info("MMOPL_RESPONSE [RELOAD STORE VALUE STATUS]", $response);

        return json_decode($response);
    }

    public function reloadTripPassStatus($order)
    {
        $data = '{
                  "data": {
                    "fare"          : "' . $order->total_price . '",
                    "supportType"   : "' . $this->media_type_id . '",
                    "qrType"        : "' . $order->product_id . '",
                    "tokenType"     : "' . $order->pass_id . '",
                    "source"        : "' . $order->src_stn_id . '",
                    "destination"   : "' . $order->des_stn_id . '",
                    "operatorId"    : "' . $this->operator_id . '",
                    "masterTxnId"   : "' . $order->ms_qr_no . '"
                  }
                }';

        GLog::info("MMOPL_REQUEST [RELOAD TRIP PASS STATUS]", $data);

        $response = Http::withHeaders(['Authorization' => $this->auth])
            ->withBody($data, 'application/json')
            ->post($this->base_url . '/qrcode/canReloadPass')
            ->collect();

        GLog::info("MMOPL_RESPONSE [RELOAD TRIP PASS STATUS]", $response);

        return json_decode($response);
    }

    public function reloadStoreValuePass($order)
    {
        $user = DB::table('users')
            ->where('pax_id', '=', $order->pax_id)
            ->first();

        $data = '{
                "data": {
                    "fare"                  : "' . $order->total_price . '",
                    "tokenType"             : "' . $order->pass_id . '",
                    "operationTypeId"       : "' . $this->op_type_id_reload . '",
                    "operatorId"            : "' . $this->operator_id . '",
                    "name"                  : "' . $user->pax_name . '",
                    "email"                 : "' . $user->pax_email . '",
                    "mobile"                : "' . $user->pax_mobile . '",
                    "trips"                 : 45,
                    "activationTime"        : "' . Carbon::now() . '",
                    "operatorTransactionId" : "' . $order->sale_or_no . '",
                    "masterTxnId"           : "' . $order->ms_qr_no . '"
                },
                "payment": {
                    "pass_price"                : "' . $order->total_price . '",
                    "pgId"                      : "' . $this->pg_id . '",
                    "pg_order_id"               : "' . $order->pg_txn_no . '"
                }
            }';

        GLog::info("MMOPL_REQUEST [RELOAD STORE VALUE PASS]", $data);

        $response = Http::withHeaders(['Authorization' => $this->auth])
            ->withBody($data, 'application/json')
            ->post($this->base_url . '/qrcode/reloadPass')
            ->collect();

        GLog::info("MMOPL_RESPONSE [RELOAD STORE VALUE PASS]", $response);

        return json_decode($response);
    }

    public function reloadTripPass($order)
    {
        $user = DB::table('users')
            ->where('pax_id', '=', $order->pax_id)
            ->first();

        $data = '{
              "data": {
                "fare"                  : "' . $order->total_price . '",
                "source"                : "' . $order->src_stn_id . '",
                "destination"           : "' . $order->des_stn_id . '",
                "tokenType"             : "' . $order->pass_id . '",
                "supportType"           : "' . $this->media_type_id . '",
                "qrType"                : "' . $order->product_id . '",
                "operationTypeId"       : "' . $this->op_type_id_reload . '",
                "operatorId"            : "' . $this->operator_id . '",
                "name"                  : "' . $user->pax_name . '",
                "email"                 : "' . $user->pax_email . '",
                "mobile"                : "' . $user->pax_mobile . '",
                "trips"                 : 45,
                "activationTime"        : "' . Carbon::now() . '",
                "operatorTransactionId" : "' . $order->sale_or_no . '",
                "masterTxnId"           : "' . $order->ms_qr_no . '"
              },
              "payment": {
                    "pass_price"                : "' . $order->total_price . '",
                    "pgId"                      : "' . $this->pg_id . '",
                    "pg_order_id"               : "' . $order->pg_txn_no . '"
              }
            }';

        GLog::info("MMOPL_REQUEST [RELOAD TRIP PASS]", $data);

        $response = Http::withHeaders(['Authorization' => $this->auth])
            ->withBody($data, 'application/json')
            ->post($this->base_url . '/qrcode/reloadPass')
            ->collect();
        GLog::info("MMOPL_REQUEST [RELOAD TRIP PASS]", $response);
        return json_decode($response);

    }

    public function genTripPass($data)
    {
        $user = DB::table('users')
            ->where('pax_id', '=', $data->pax_id)
            ->first();

        $request = '{
                    "data": {
                        "fare"                      : "' . $data->total_price . '",
                        "supportType"               : "' . $data->media_type_id . '",
                        "qrType"                    : "' . $data->product_id . '",
                        "tokenType"                 : "' . $data->pass_id . '",
                        "operationTypeId"           : "' . $this->op_type_id_issue . '",
                        "source"                    : "' . $data->src_stn_id . '",
                        "destination"               : "' . $data->des_stn_id . '",
                        "operatorId"                : "' . $this->operator_id . '",
                        "name"                      : "' . $user->pax_name . '",
                        "email"                     : "' . $user->pax_email . '",
                        "mobile"                    : "' . $user->pax_mobile . '",
                        "activationTime"            : "' . Carbon::now() . '",
                        "operatorTransactionId"     : "' . $data->sale_or_no . '"
                    },
                    "payment": {
                           "pass_price"                : "' . $data->total_price . '",
                           "pgId"                      : "' . $this->pg_id . '",
                           "pg_order_id"               : "' . $data->pg_txn_no . '"
                    }
                }';

        GLog::info("MMOPL_REQUEST [GENERATE TRIP PASS]", $request);

        $response = Http::withHeaders(['Authorization' => $this->auth])
            ->withBody($request, 'application/json')
            ->post($this->base_url . '/qrcode/issuePass')
            ->collect();

        GLog::info("MMOPL_RESPONSE [GENERATE TRIP PASS]", $response);

        return json_decode($response);
    }

    public function getRefundInfo($data)
    {
        $op_id = $this->operator_id;
        $pass_id = $data->pass_id;
        $master_id = $data->ms_qr_no;

        GLog::info("MMOPL_REQUEST [GET REFUND INFO]", $data);

        $response = Http::withHeaders(['Authorization' => $this->auth])
            ->get($this->base_url . "/qrcode/refund/info?tokenType=$pass_id&masterTxnId=$master_id&operatorId=$op_id")
            ->collect();

        GLog::info("MMOPL_RESPONSE [GET REFUND INFO]", $response);

        return json_decode($response);
    }

    public function refundTicket($data, $refund_or_no)
    {
        $data = '{
                "data" : {
                    "operatorId"                        :"' . $data->data->operatorId . '",
                    "supportType"                       :"' . $data->data->supportType . '",
                    "qrType"                            :"' . $data->data->qrType . '",
                    "tokenType"                         :"' . $data->data->tokenType . '",
                    "source"                            :"' . $data->data->source . '",
                    "destination"                       :"' . $data->data->destination . '",
                    "remainingBalance"                  :"' . $data->data->remainingBalance . '",
                    "details":{
                        "registration":{
                            "processingFee"              : "' . $data->data->details->registration->processingFee . '",
                            "refundType"                 : "' . $data->data->details->registration->refundType . '",
                            "processingFeeAmount"        : "' . $data->data->details->registration->processingFeeAmount . '",
                            "refundAmount"               : "' . $data->data->details->registration->refundAmount . '",
                            "passPrice"                  : "' . $data->data->details->registration->passPrice . '"
                        },
                        "pass" : {
                            "processingFee"              : "' . $data->data->details->pass->processingFee . '",
                            "refundType"                 : "' . $data->data->details->pass->refundType . '",
                            "processingFeeAmount"        : "' . $data->data->details->pass->processingFeeAmount . '",
                            "refundAmount"               : "' . $data->data->details->pass->refundAmount . '",
                            "passPrice"                  : "' . $data->data->details->pass->passPrice . '"
                        }
                    },
                    "operatorTransactionId"             :"' . $refund_or_no . '",
                    "operationTypeId"                   :"' . $this->op_type_id_issue . '",
                    "masterTxnId"                       :"' . $data->data->masterTxnId . '",
                    "pgId"                              :"' . $this->pg_id . '"
                }
             }';

        GLog::info("MMOPL_REQUEST [REFUND TICKET]", $data);

        $response = Http::withHeaders(['Authorization' => $this->auth])
            ->withBody($data, 'application/json')
            ->post($this->base_url . '/qrcode/refund')
            ->collect();

        GLog::info("MMOPL_RESPONSE [REFUND TICKET]", $response);

        return json_decode($response);

    }

    public function graInfo($slave_id, $station_id)
    {

        GLog::info("MMOPL_REQUEST [GET GRA INFO]", $slave_id . " | " . $station_id);

        $response = Http::withHeaders(['Authorization' => $this->auth])
            ->get($this->base_url . "/qrcode/penalty/status?transactionId=$slave_id&station=$station_id")
            ->collect();

        GLog::info("MMOPL_RESPONSE [GET GRA INFO]", $response);

        return json_decode($response);
    }

    public function applyGra($response, $order)
    {

        $data = '{
                "data": {
                    "fare"                      : "' . $order->total_price . '",
                    "destination"               : "' . $order->des_stn_id . '",
                    "refTxnId"                  : "' . $order->ref_sl_qr . '",
                    "tokenType"                 : "' . $order->pass_id . '",
                    "supportType"               : "' . $this->media_type_id . '",
                    "qrType"                    : "' . $order->product_id . '",
                    "operatorId"                : "' . $this->operator_id . '",
                    "operatorTransactionId"     : "' . $order->sale_or_no . '",
                    "activationTime"            : "' . time() . '",
                    "freeExitOptionId"          : 0,
                    "penalties"                 : ' . json_encode($response->data->penalties, JSON_OBJECT_AS_ARRAY) . ',
                    "overTravelCharges"         : ' . json_encode($response->data->overTravelCharges, JSON_OBJECT_AS_ARRAY) . '
                },
                "payment": {
                    "pass_price"                : "' . $order->total_price . '",
                    "pgId"                      : "' . $this->pg_id . '",
                    "pg_order_id"               : "' . $order->pg_txn_no . '"
                }
            }';

        GLog::info("MMOPL_REQUEST [APPLY GRA]", $data);

        $response = Http::withHeaders(['Authorization' => $this->auth])
            ->withBody(json_encode(json_decode($data)), 'application/json')
            ->post($this->base_url . '/qrcode/penalty/issueToken')
            ->collect();

        GLog::info("MMOPL_RESPONSE [APPLY GRA]", $response);

        return json_decode($response);

    }

    public function canIssuePass($product_id, $pass_id)
    {
        $data = '{
                "data": {
                    "fare"          : "1100",
                    "mobile"        : "' . Auth::user()->pax_mobile . '",
                    "operatorId"    : "' . $this->operator_id . '",
                    "qrType"        : "' . $product_id . '",
                    "supportType"   : "' . $this->media_type_id . '",
                    "tokenType"     : "' . $pass_id . '"
                }
            }';

        GLog::info("MMOPL_REQUEST [CAN ISSUE PASS]", $data);

        $response = Http::withHeaders(['Authorization' => $this->auth])
            ->withBody($data, 'application/json')
            ->post($this->base_url . '/qrcode/canIssuePass');

        GLog::info("MMOPL_RESPONSE [CAN ISSUE PASS]", $response);

        return json_decode($response);

    }

    public function canIssuePassTP($product_id, $pass_id)
    {
        $data = '{
                "data": {
                    "fare"          : "1100",
                    "mobile"        : "' . Auth::user()->pax_mobile . '",
                    "operatorId"    : "' . $this->operator_id . '",
                    "qrType"        : "' . $product_id . '",
                    "source": 1,
                    "destination": 2,
                    "supportType"   : "' . $this->media_type_id . '",
                    "tokenType"     : "' . $pass_id . '"
                }
            }';

        GLog::info("MMOPL_REQUEST [CAN ISSUE TP PASS]", $data);

        $response = Http::withHeaders(['Authorization' => $this->auth])
            ->withBody($data, 'application/json')
            ->post($this->base_url . '/qrcode/canIssuePass');

        GLog::info("MMOPL_RESPONSE [CAN ISSUE TP PASS]", $response);

        return json_decode($response);

    }

    /*public function order_status($order_id)
    {

        print_r($order_id);
        $response = Http::withHeaders(['Authorization' => $this->auth])
            ->get($this->base_url . "/qrcode/bookings/operatorOrder?operator_order_id=$order_id")
            ->collect();
        return json_decode($response);


    }*/

}
