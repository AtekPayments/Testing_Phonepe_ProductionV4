<?php /** @noinspection LaravelFunctionsInspection */

namespace App\Http\Controllers\Modules\Processing;

use App\Http\Controllers\Api\MMOPL\ApiController;
use App\Http\Controllers\Api\PhonePe\PhonePeStatusController;
use App\Http\Controllers\Controller;
use App\Models\RjtSlBooking;
use App\Models\SjtSlBooking;
use App\Models\SvSlBooking;
use App\Models\TpSlBooking;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ProcessingController extends Controller
{
    public function index($order_id)
    {
        return Inertia::render('Modules/Processing/Processing', [
            'order' => $order_id
        ]);
    }

    public function init($order)
    {
        Log::info("CONTROLLER -> PROCESSING");

        if ($order->op_type_id == env('ISSUE')) {
            if ($order->product_id == env('PRODUCT_SJT') || $order->product_id == env('PRODUCT_RJT')) {
                $this->genTicket($order);
            } else {
                $this->genPass($order);
            }
        } elseif ($order->op_type_id == env('RELOAD')) {
            $this->reloadPass($order);
        } else {
            $this->graTransaction($order);
        }

    }

    private function genTicket($order): void
    {
        $api = new ApiController();
        $response = $api->genSjtRjtTicket($order);

        if (is_null($response) || $response->status == "BSE") {
            $this->updateOrderStatus($response, $order);
        } else {
            if ($order->product_id == env('PRODUCT_SJT')) {
                foreach ($response->data->trips as $trip) {
                    SjtSlBooking::store($response, $trip, $order);
                }
            } else {
                foreach ($response->data->trips as $trip) {
                    RjtSlBooking::store($response, $trip, $order);
                }
            }
            $this->updateOrderStatus($response, $order);
        }
    }

    private function genPass($order)
    {
        $api = new ApiController();

        $response = $order->product_id == env('PRODUCT_SV')
            ? $api->genStoreValuePass($order)
            : $api->genTripPass($order);

        $this->updateOrderStatus($response, $order);

    }

    private function reloadPass($order)
    {
        $api = new ApiController();
        $response = $order->product_id == env('PRODUCT_SV')
            ? $api->reloadStoreValuePass($order)
            : $api->reloadTripPass($order);

        $this->updateReloadOrderStatus($response, $order);

    }

    private function graTransaction($order)
    {
        $api = new ApiController();
        $statusResponse = $api->graInfo($order->ref_sl_qr, $order->des_stn_id);

        if (is_null($statusResponse) || $statusResponse->status == "BSE") {
            $this->updateGRAOrderStatus($statusResponse, $order, true);
        } else {

            $response = $api->applyGra($statusResponse, $order);

            if (!is_null($response) && $response->status != "BSE") {

                $old_order = DB::table('sale_order')
                    ->where('ms_qr_no', '=', $order->ms_qr_no)
                    ->first();

                if ($order->product_id == env('PRODUCT_SJT')) SjtSlBooking::store($response, $response->data->trips[0], $old_order);
                else if ($order->product_id == env('PRODUCT_RJT')) RjtSlBooking::store($response, $response->data->trips[0], $old_order);
                else if ($order->product_id == env('PRODUCT_SV')) SvSlBooking::store($old_order, $response);
                else TpSlBooking::store($old_order, $response);

            }

            $this->updateGRAOrderStatus($response, $order, false);
        }
    }

    public function getOrderDetails($order_id)
    {
        $data = DB::table('sale_order')
            ->where('sale_or_no', '=', $order_id)
            ->orderBy('sale_or_id', 'desc')
            ->first();



        if($data->sale_or_status == env('ORDER_GENERATED')){
            $api = new PhonePeStatusController();
            $res = $api->getPaymentStatus($data);
            if($res->code == "PAYMENT_SUCCESS"){
                DB::table('sale_order')
                    ->where('sale_or_no', '=', $order_id)
                    ->update([
                        'pg_txn_no' => $res->data->providerReferenceId,
                    ]);

                $this->init($data);
            }
        }

        if($data->sale_or_status == env('ORDER_TICKET_GENERATION_FAILED')){
            DB::table('sale_order')
                ->where('sale_or_no','=',$order_id)
                ->where('mm_ms_acc_id','!=', null)
                ->update([
                    'sale_or_status' => env('ORDER_TICKET_GENERATED')
                ]);
        }


        $reData = DB::table('sale_order')
            ->where('sale_or_no', '=', $order_id)
            ->orderBy('sale_or_id', 'desc')
            ->first();

        $old_order = DB::table('sale_order')
            ->where('ms_qr_no', '=', $data->ms_qr_no)
            ->first();

        return response([
            'status' => true,
            'response' => $reData,
            'order_id' => $old_order->sale_or_no
        ]);

    }

    public function updateOrderStatus($response, $order)
    {

        if ($response->status == "OK") {

            DB::table('sale_order')
                ->where('sale_or_no', '=', $order->sale_or_no)
                ->update([
                    'ms_qr_no' => $response->data->masterTxnId,
                    'mm_ms_acc_id' => $response->data->transactionId,
                    'ms_qr_exp' => Carbon::createFromTimestamp($response->data->masterExpiry)->toDateTimeString(),
                    'sale_or_status' => env('ORDER_TICKET_GENERATED')
                ]);

        } else {

            DB::table('sale_order')
                ->where('sale_or_no', '=', $order->sale_or_no)
                ->update([
                    'sale_or_status' => env('ORDER_TICKET_GENERATION_FAILED')
                ]);

        }

    }

    public function updateReloadOrderStatus($response, $order)
    {

        if ($response->status == "OK") {

            DB::table('sale_order')
                ->where('sale_or_no', '=', $order->sale_or_no)
                ->update([
                    'ms_qr_no' => $response->data->masterTxnId,
                    'mm_ms_acc_id' => $response->data->transactionId,
                    'ms_qr_exp' => Carbon::createFromTimestamp($response->data->masterExpiry)->toDateTimeString(),
                    'sale_or_status' => env('ORDER_RELOADED')
                ]);

        } else {

            DB::table('sale_order')
                ->where('sale_or_no', '=', $order->sale_or_no)
                ->update([
                    'sale_or_status' => env('ORDER_RELOADED_FAIL')
                ]);

        }

    }

    public function updateGRAOrderStatus($response, $order, $isInfo)
    {

        if ($response->status == "OK") {

            if (!$isInfo) {
                DB::table('sale_order')
                    ->where('sale_or_no', '=', $order->sale_or_no)
                    ->update([
                        'ms_qr_no' => $response->data->masterTxnId,
                        'mm_ms_acc_id' => $response->data->transactionId,
                        'ms_qr_exp' => Carbon::createFromTimestamp($response->data->masterExpiry)->toDateTimeString(),
                        'sale_or_status' => env('ORDER_GRA')
                    ]);
            }

        } else {

            DB::table('sale_order')
                ->where('sale_or_no', '=', $order->sale_or_no)
                ->update([
                    'sale_or_status' => env('ORDER_GRA_FAIL')
                ]);

        }

    }

}
