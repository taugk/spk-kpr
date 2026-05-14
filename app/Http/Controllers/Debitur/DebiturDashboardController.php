<?php

namespace App\Http\Controllers\Debitur;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class DebiturDashboardController extends Controller
{
    public function index()
    {
        return view('debitur.pages.dashboard.index', [
            'title' => 'Dashboard Debitur',
        ]);
    }
}
