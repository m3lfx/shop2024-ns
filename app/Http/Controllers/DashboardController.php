<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;

use App\Charts\CustomerChart;
use App\Charts\SalesChart;
use App\Charts\ItemChart;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->bgcolor = collect([
            '#7158e2',
            '#3ae374',
            '#ff3838',
            "#FF851B",
            "#7FDBFF",
            "#B10DC9",
            "#FFDC00",
            "#001f3f",
            "#39CCCC",
            "#01FF70",
            "#85144b",
            "#F012BE",
            "#3D9970",
            "#111111",
            "#AAAAAA",
        ]);
    }


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

        // SELECT sum(ol.quantity * i.sell_price), monthname(o.date_placed) FROM orderinfo o inner join orderline ol on o.orderinfo_id = ol.orderinfo_id inner join item i on i.item_id = ol.item_id group by month(o.date_placed);

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
            'line',
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

        // SELECT sum(ol.quantity), i.description FROM orderline ol inner join item i on i.item_id = ol.item_id group by i.description;

        $items = DB::table('orderline AS ol')
            ->join('item AS i', 'ol.item_id', '=', 'i.item_id')
            ->groupBy('i.description')
            ->orderBy('total', 'DESC')
            ->pluck(DB::raw('sum(ol.quantity) AS total'), 'description')
            ->all();
        // dd($items);
        $itemChart = new ItemChart();
        // dd(array_values($customer));

        $dataset = $itemChart->labels(array_keys($items));
        // dd($dataset);
        $dataset = $itemChart->dataset(
            'Item sold',
            'doughnut',
            array_values($items)
        );

        $dataset = $dataset->backgroundColor($this->bgcolor);

        $dataset = $dataset->fill(false);
        $itemChart->options([
            'responsive' => true,
            'legend' => ['display' => true],
            'tooltips' => ['enabled' => true],
            'aspectRatio' => 1,

            'scales' => [
                'yAxes' => [
                    'display' => true,
                    'ticks' => ['beginAtZero' => true],
                    'gridLines' => ['display' => false],
                    'ticks' => [
                        'beginAtZero' => true,
                        'min' => 0,
                        'stepSize' => 1,
                    ]
                ],
                'xAxes' => [
                    'categoryPercentage' => 0.8,
                    //'barThickness' => 100,
                    'barPercentage' => 1,

                    'gridLines' => ['display' => false],
                    'display' => true,
                    'ticks' => [
                        'beginAtZero' => true,
                        'min' => 0,
                        'stepSize' => 1,
                    ],
                ],
            ],
        ]);

        return view('dashboard.index', compact('customerChart', 'salesChart', 'itemChart'));
    }
}
