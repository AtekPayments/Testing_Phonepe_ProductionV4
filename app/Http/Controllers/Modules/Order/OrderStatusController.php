<?php

namespace App\Http\Controllers\Modules\Order;

use App\Http\Controllers\Api\MMOPL\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Modules\Utility\OrderUtility;
use App\Models\RjtSlBooking;
use App\Models\SjtSlBooking;

use App\Models\SvSlBooking;
use App\Models\TpSlBooking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Response;


class OrderStatusController extends Controller
{
    public function order_status()
    {
        $response = [];

        $orders = DB::table('sale_order')
            ->where('pax_id', '=', Auth::id())
            ->where('sale_or_status', '=', env('ORDER_PAYMENT_SUCCESS'))
            ->get();

        if ($orders == null) {
            return response([
                'status' => false,
                'response' => "No sale order found"
            ]);
        }

        foreach ($orders as $order) {

            $api = new ApiController();
            $response = $api->order_status($order->sale_or_no);

            if ($response == null) {
                return response([
                    'status' => false,
                    'error' => 'Order id not exist'
                ]);

            } else if ($response->status == "BSE") {

                return response([
                    'status' => false,
                    'message' => $response
                ]);

            } else {
                if ($response->data->qrType == env('PRODUCT_SJT')) {
                    $response[] = $this->sjt_status_insert($response, $order);
                } else if ($response->data->qrType == env('PRODUCT_RJT')) {
                    $response[] = $this->rjt_status_insert($response, $order);
                } else if ($response->data->qrType == env('PRODUCT_SV')) {
                    $response[] = $this->sv_status_insert($response, $order);
                } else if ($response->data->qrType == env('PRODUCT_TP')) {
                    $response[] = $this->tp_status_insert($response, $order);
                } else {
                    return response([
                        'status' => false,
                        'response' => "Invalid Product ID"
                    ]);
                }
            }
        }
        return $response;
    }

    public function sjt_status_insert($response, $order)
    {

        $ticket_check = DB::table('sjt_sl_booking')
            ->where('sale_or_id', '=', $order->sale_or_id)
            ->orderBy('id', 'desc')
            ->first();


        if ($ticket_check == null) {

            foreach ($response->data->trips as $trip) {
                SjtSlBooking::store($response, $trip, $order);
            }

            return response([
                'status' => true,
                'response' => "Ticket inserted in DB successfully"
            ]);

        } else {

            return response([
                'status' => false,
                'response' => "Ticket already in SJT Database"
            ]);


        }

    }

    public function rjt_status_insert($response, $order)
    {
        $ticket_check = DB::table('rjt_sl_booking')
            ->where('sale_or_id', '=', $order->sale_or_id)
            ->orderBy('id', 'desc')
            ->first();


        if ($ticket_check == null) {
            $utility = new OrderUtility();
            $demo = $utility->updateSaleOrder($order, $response);

            foreach ($response->data->trips as $trip) {
                RjtSlBooking::store($response, $trip, $order);
            }

            return response([
                'status' => true,
                'response' => "Ticket inserted in DB successfully"
            ]);

        } else {

            return response([
                'status' => false,
                'response' => "Ticket already in RJT Database"
            ]);


        }
    }

    public function sv_status_insert($response, $order)
    {
        $ticket_check = DB::table('sv_sl_booking')
            ->where('sale_or_id', '=', $order->sale_or_id)
            ->orderBy('id', 'desc')
            ->first();


        if ($ticket_check == null) {

            $utility = new OrderUtility();
            $demo = $utility->updateSaleOrder($order, $response);
            SvSlBooking::store($order, $response);

            return response([
                'status' => true,
                'response' => "Ticket inserted in DB successfully"
            ]);

        } else {

            return response([
                'status' => false,
                'response' => "Ticket already in SV Database"
            ]);


        }
    }

    public function tp_status_insert($response, $order)
    {
        $ticket_check = DB::table('tp_sl_booking')
            ->where('sale_or_id', '=', $order->sale_or_id)
            ->orderBy('id', 'desc')
            ->first();


        if ($ticket_check == null) {
            $utility = new OrderUtility();
            $demo = $utility->updateSaleOrder($order, $response);

            $tpSl = new TpSlBooking();
            $tpSl->store($order, $response);

            return response([
                'status' => true,
                'response' => "Ticket inserted in DB successfully"
            ]);

        } else {

            return response([
                'status' => false,
                'response' => "Ticket already in TP Database"
            ]);

        }
    }

}


