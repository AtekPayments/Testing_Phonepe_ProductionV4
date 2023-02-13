<?php

namespace App\Http\Controllers\Modules\Ticket;

use App\Http\Controllers\Api\PhonePe\PhonePePaymentController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Modules\Utility\GLog;
use App\Http\Controllers\Modules\Utility\OrderUtility;
use App\Models\SaleOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TicketOrderController extends Controller
{

    public function index()
    {
        return Inertia::render('Modules/Ticket/Order', [
            'stations' => DB::table('stations')->get(['stn_id', 'stn_name'])
        ]);
    }

    public function indexRecent($source, $destination)
    {
        return Inertia::render('Modules/Ticket/Order', [
            'stations' => DB::table('stations')->get(['stn_id', 'stn_name']),
            'source' => $source,
            'destination' => $destination
        ]);
    }

    /*
        CHECK IF USER HAS
        ANY UNPAID ORDERS
    */
    public function isPending()
    {
        $pendingOrders = DB::table('sale_order')
            ->where('pax_id', Auth::id())
            ->where('sale_or_status', '=', env('ORDER_GENERATED'))
            ->get()->count();

        return $pendingOrders > 0
            ? response(['isPendingPayment' => true])
            : response(['isPendingPayment' => false]);

    }

    /*
        CREATE NEW ORDER
    */
    public function create(Request $request)
    {

        GLog::title("ORDER PROCESS STARTED");

        $request->validate([
            'source_id' => ['required'],
            'destination_id' => ['required'],
            'pass_id' => ['required'],
            'quantity' => ['required'],
            'fare' => ['required']
        ]);

        GLog::info("ORDER REQUEST", $request->getContent());

        $saleOrderNumber = OrderUtility::genSaleOrderNumber(
            $request->input('pass_id')
        );

        SaleOrder::store($request, $saleOrderNumber);

        $order = DB::table('sale_order as so')
            ->join('stations as s', 's.stn_id', '=', 'so.src_stn_id')
            ->join('stations as d', 'd.stn_id', '=', 'so.des_stn_id')
            ->where('sale_or_no', '=', $saleOrderNumber)
            ->select(['so.*', 's.stn_name as source_name', 'd.stn_name as destination_name'])
            ->first();

        $api = new PhonePePaymentController();
        $response = $api->pay($order);

        if (is_null($response)) {
            return response([
                'status' => false,
                'error' => $response,
                'order_id' => $saleOrderNumber
            ]);
        } else {
            return $response->success
                ? response([
                    'status' => true,
                    'redirectUrl' => $response->data->redirectUrl,
                    'order_id' => $saleOrderNumber
                ])
                : response([
                    'status' => false,
                    'error' => $response,
                    'order_id' => $saleOrderNumber
                ]);
        }
    }
}
