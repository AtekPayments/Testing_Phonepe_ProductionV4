<?php

namespace App\Http\Controllers\Modules\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GLog extends Controller
{
    public static function title($title)
    {
        Log::info("\n\n\n\n----------------------------------------------- $title -----------------------------------------------");
    }

    public static function info($title, $data)
    {
        Log::info("\n\ $title -> " . json_encode($data));
    }

}
