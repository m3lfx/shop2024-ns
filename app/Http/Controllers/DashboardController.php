<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;

use App\Charts\CustomerChart;
use App\Charts\SalesChart;

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

        $sales = DB::table('orderinfo AS o')
            ->join('orderline AS ol', 'o.orderinfo_id', '=', 'ol.orderinfo_id')
            ->join('item AS i', 'ol.item_id', '=', 'i.item_id')
            ->orderBy(DB::raw('month(o.date_placed)'), 'ASC')
            ->groupBy(DB::raw('monthname(o.date_placed)'))
            ->pluck(
                DB::raw('sum(ol.quantity * i.sell_price) AS total'),
                DB::raw('monthname(o.date_placed) AS month')
            )
            ->all();
        // dd($sales);
        $salesChart = new SalesChart();
        $dataset = $salesChart->labels(array_keys($sales));
        $dataset = $salesChart->dataset(
            'Customer Demographics',
            'horizontalBar',
            array_values($sales)
        );
        $dataset = $dataset->backgroundColor([
            '#7158e2',
            '#3ae374',
            '#ff3838',
        ]);
        $salesChart->options([
            'backgroundColor' => '#fff',
            'fill' => false,
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

        return view('dashboard.index', compact('customerChart', 'salesChart'));
    }
    
}
