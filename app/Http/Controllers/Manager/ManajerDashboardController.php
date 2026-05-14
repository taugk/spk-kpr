<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class ManajerDashboardController extends Controller
{
    public function index (){
        return view('manager.pages.dashboard.index');
    }
}
