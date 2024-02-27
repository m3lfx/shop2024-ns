<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class DashboardController extends Controller
{
    
    
    public function index() {
        if (Auth::user()->role !== 'admin') {
            return 'unauthorized';
        }
        return 'admin';
    }
}
