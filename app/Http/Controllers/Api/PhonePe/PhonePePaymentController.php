<?php

namespace App\Http\Controllers\Api\PhonePe;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Modules\Utility\GLog;
use Illuminate\Support\Facades\Http;

class PhonePePaymentController extends Controller
{
    public $salt_key;
    public $app_url;
    public $salt_index;
    public $x_client_id;
    public $phonepe_base_url;

    public function __construct()
    {
        $this->salt_key = env('PHONEPAY_SLAT_KEY');
        $this->salt_index = env("PHONEPAY_SLAT_INDEX");
        $this->x_client_id = env("PHONEPAY_CLIENT_ID");
        $this->app_url = env('APP_URL');
        $this->phonepe_base_url = env("PHONEPAY_BASE_URL");
    }

    public function pay($order)
    {
        $cert = $this->createCart($order);
        $payload = $this->createPayload($order, $cert);
        $request = $this->createRequest($payload);
        $x_verify = $this->createXVerify($payload);

        GLog::info("PAYMENT REQUEST", $request);

        $response = Http::withBody($request, 'application/json')
            ->withHeaders([
                'X-CALLBACK-URL' => $this->app_url . '/api/payment/s2s/'. $order->sale_or_no,
                'X-CLIENT-ID' => $this->x_client_id,
                'X-VERIFY' => $x_verify
            ])
            ->post("$this->phonepe_base_url/v3/transaction/sdk-less/initiate")->collect();

        GLog::info("PAYMENT RESPONSE", $response);

        return json_decode($response);

    }

    private function createCart($order): string
    {
        $url = $this->app_url . '/order/' . $order->sale_or_no;

        if ($order->op_type_id == env('ISSUE')) {
            if ($order->product_id == env('PRODUCT_SV')) {

                return '{
                "orderContext": {
                    "trackingInfo": {
                        "type": "HTTPS",
                        "url":"' . $url . '"
                    }
                },
                "fareDetails": {
                    "totalAmount":' . $order->total_price * 100 . ',
                    "payableAmount":' . $order->total_price * 100 . '
                },
                "cartDetails": {
                    "cartItems": [
                        {
                            "category": "SHOPPING",
                            "itemId":"' . $order->sale_or_no . '",
                            "price":' . $order->total_price * 100 . ',
                            "itemName": "Store Value Pass",
                            "quantity": 1,
                            "shippingInfo": {
                                "deliveryType": "STANDARD",
                                "time": {
                                    "timestamp": ' . time() . ',
                                    "zoneOffSet": "+05:30"
                                }
                            }
                        }
                    ]
                }
            }';

            }

            return '{
                "orderContext": {
                    "trackingInfo": {
                        "type": "HTTPS",
                        "url":"' . $url . '"
                    }
                },
                "fareDetails": {
                    "totalAmount":' . $order->total_price * 100 . ',
                    "payableAmount":' . $order->total_price * 100 . '
                },
                "cartDetails": {
                    "cartItems": [
                        {
                            "category": "TRAIN",
                            "itemId":"' . $order->sale_or_no . '",
                            "price":' . $order->total_price * 100 . ',
                            "from": {
                                "stationName": "' . $order->source_name . '"
                            },
                            "to": {
                                "stationName": "' . $order->destination_name . '"
                            },
                            "dateOfTravel": {
                                "timestamp": ' . time() . ',
                                "zoneOffSet": "+05:30"
                            },
                            "numOfPassengers": ' . $order->unit . '
                        }
                    ]
                }
            }';

        } else {
            return '{
                "orderContext": {
                    "trackingInfo": {
                        "type": "HTTPS",
                        "url":"' . $url . '"
                    }
                },
                "fareDetails": {
                    "totalAmount":' . $order->total_price * 100 . ',
                    "payableAmount":' . $order->total_price * 100 . '
                },
                "cartDetails": {
                    "cartItems": [
                        {
                            "category": "TRAIN",
                            "itemId":"' . $order->sale_or_no . '",
                            "price":' . $order->total_price * 100 . ',
                            "from": {
                                "stationName": ""
                            },
                            "to": {
                                "stationName": "' . $order->des_stn_id . '"
                            },
                            "dateOfTravel": {
                                "timestamp": ' . time() . ',
                                "zoneOffSet": "+05:30"
                            },
                            "numOfPassengers": ' . $order->unit . '
                        }
                    ]
                }
            }';
        }


    }

    private function createPayload($order, $cert)
    {
        $payload = new Payload();
        $payload->merchantId = $this->x_client_id;
        $payload->amount = $order->total_price * 100;
        $payload->validFor = 300000;
        $payload->transactionId = $order->sale_or_no;
        $payload->merchantOrderId = $order->sale_or_no;
        $payload->redirectUrl = $this->app_url . '/processing/' . $order->sale_or_no;
        $payload->transactionContext = "" . base64_encode($cert) . "";

        return json_encode($payload, JSON_FORCE_OBJECT);

    }

    private function createRequest($payload): string
    {
        return '{"request": "' . base64_encode($payload) . '"}';
    }

    private function createXVerify($payload): string
    {
        $hash = hash('sha256', base64_encode($payload) . "/v3/transaction/sdk-less/initiate" . $this->salt_key);
        return $hash . "###" . $this->salt_index;
    }

}
