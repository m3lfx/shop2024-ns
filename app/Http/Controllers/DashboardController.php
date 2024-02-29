<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;

use App\Charts\CustomerChart;

class DashboardController extends Controller
{


    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('user.login');
        }
        $customer = DB::table('customer')
            ->whereNotNull('addressline')
            ->groupBy('addressline')
            ->orderBy('total')
            ->pluck(DB::raw('count(addressline) as total'), 'addressline')
            ->all();
        // dd($customer);
        $customerChart = new CustomerChart();
        // dd(array_values($customer));
        $dataset = $customerChart->labels(array_keys($customer));
        $dataset = $customerChart->dataset(
            'Customer Demographics',
            'bar',
            array_values($customer)
        );
        $dataset = $dataset->backgroundColor([
            '#7158e2',
            '#3ae374',
            '#ff3838',
        ]);
        $customerChart->options([
            'responsive' => true,
            'legend' => ['display' => true],
            'tooltips' => ['enabled' => true],
            'aspectRatio' => 1,
            'scales' => [
                'yAxes' => [
                    [
                        'display' => true,
                    ],
                ],
                'xAxes' => [
                    [
                        'gridLines' => ['display' => false],
                        'display' => true,
                    ],
                ],
            ],
        ]);
        return view('dashboard.index', compact('customerChart'));
    }
    
}
