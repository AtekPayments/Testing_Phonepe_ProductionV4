<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Products', [
            'products' => [
                [
                    'id' => 1,
                    'name' => 'Metro QR Ticket',
                    'description' => 'QR ticket for one-way or return journey',
                    'img' => '/img/qr.gif',
                    'url' => '/ticket/dashboard'
                ],
                [
                    'id' => 2,
                    'name' => 'Store Value Pass',
                    'description' => 'A digital metro card for your easy travelling and conveyance',
                    'img' => '/img/sv_pass.gif',
                    'url' => '/sv/dashboard'
                ],
                [
                    'id' => 3,
                    'name' => 'Trip Pass',
                    'description' => 'Super saver pass for customers who regularly travel between two fixed stations',
                    'img' => '/img/tp_pass.gif',
                    'url' => '/tp/dashboard'
                ],
            ]
        ]);
    }
}
