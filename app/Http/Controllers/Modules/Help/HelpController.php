<?php

namespace App\Http\Controllers\Modules\Help;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class HelpController extends Controller
{
    public function index($slave_id, $order_id)
    {
        return Inertia::render('Help/Index', [
            'slave_id' => $slave_id,
            'order_id' => $order_id
        ]);
    }
}
