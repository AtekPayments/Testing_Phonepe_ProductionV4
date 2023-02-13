<?php

namespace App\Http\Controllers\Modules\Payment;

use App\Http\Controllers\Api\PhonePe\PhonePePaymentController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index($order_id)
    {
        return Inertia::render('Payment/Payment', [
            'order' => $order_id
        ]);
    }

    public function init($order_id)
    {
        $order = DB::table('sale_order as so')
            ->join('stations as s', 's.stn_id', '=', 'so.src_stn_id')
            ->join('stations as d', 'd.stn_id', '=', 'so.des_stn_id')
            ->where('sale_or_no', '=', $order_id)
            ->select(['so.*', 's.stn_name as source_name', 'd.stn_name as destination_name'])
            ->first();

        if (is_null($order)) $order = DB::table('sale_order')
            ->where('sale_or_no', '=', $order_id)
            ->first();

        $api = new PhonePePaymentController();
        $response = $api->pay($order);


        if ($response->success) {
            return response([
                'status' => true,
                'redirectUrl' => $response->data->redirectUrl
            ]);
        }

        return response([
            'status' => false,
            'error' => $response->code
        ]);

    }

    public function failed($order_id)
    {
        DB::table('sale_order')
            ->where('sale_or_no', '=', $order_id)
            ->where('sale_or_status', '=', env('ORDER_GENERATED'))
            ->update([
                'sale_or_status' => env('ORDER_PAYMENT_FAILED'),
            ]);

        return redirect()->route('products');
    }

}
