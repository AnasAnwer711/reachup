<?php

namespace App\Http\Controllers;

use App\AdminNotification;
use App\AdvisorDetail;
use App\Chart;
use App\PaypalTransaction;
use App\User;
use App\UserReachup;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $periodLabels;

    private function getMonthDays()
    {
        $days = [];
        for ($i = 1; $i < date('t'); $i++) {
            $days[] = $i;
        }
        return $days;
    }

    public function __construct()
    {
        $this->periodLabels = [
            'Year' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'Month' => $this->getMonthDays(),
            'Quarter' => ['Jan-Mar', 'Apr-Jun', 'Jul-Sep', 'Oct-Dec']
        ];
    }

    public function get_admin_notificaitons($old_count = 0)
    {
        $count = AdminNotification::count();
        $admin_notifications = AdminNotification::orderBy('id', 'DESC')->get();
        return response()->json(['data'=>$admin_notifications, 'count'=>$count]);
    }

    public function index(Request $request)
    {

        $reachup_count = UserReachup::count();
        $user_count = User::where('user_type_id', 1)->count();
        $professional_count = AdvisorDetail::where('status', 'active')->count();
        $total_earned = PaypalTransaction::whereHas('reachup', function($q){
            $q->where('status', 'completed');
        })->sum('reachup_fee');
        // dd($total_earned);
        $user_reachups = UserReachup::where('status', '!=', 'pending')->where('status', '!=', 'reject')->selectRaw("count(*) data");

        // $chart = new Chart();
        // $period = isset($request->period) ? $request->period : 'Year';
        // $chart->labels = $this->periodLabels['Year'];
        // // $chart->current_reachups = [0,0,5,3,15,20,25,150,0,89,0,12];
        // $prevFrom = date('Y-m-d', strtotime('first day of january previous year'));
        // $prevTo = date('Y-m-d', strtotime('last day of december previous year'));
        // $fromDate = date('Y-m-d', strtotime('first day of january this year'));
        // $toDate = date('Y-m-d', strtotime('last day of december this year'));
        // $chart->current_reachups = $this->dataset($user_reachups, $period, $fromDate, $toDate);

        // // $chart->previous_reachups = [10,0,15,3,125,0,25,52,0,0,111,12];
        // $chart->previous_reachups = $this->dataset($user_reachups, $period, $prevFrom, $prevTo);

        return view('dashboard.index', compact('reachup_count', 'user_count','professional_count', 'total_earned'));
    }

    public function reachupchart(Request $request)
    {
        // dd($request->all());
        $user_reachups = UserReachup::where('status', '!=', 'pending')->where('status', '!=', 'reject')->selectRaw("count(*) data");

        $chart = new Chart();
        $period = isset($request->period) ? $request->period : 'Year';
        $chart->labels = $this->periodLabels[isset($request->period) ? $request->period : 'Year'] ;
        // $chart->current_reachups = [0,0,5,3,15,20,25,150,0,89,0,12];
        $prevFrom = date('Y-m-d', strtotime('first day of january previous year'));
        $prevTo = date('Y-m-d', strtotime('last day of december previous year'));
        $fromDate = date('Y-m-d', strtotime('first day of january this year'));
        $toDate = date('Y-m-d', strtotime('last day of december this year'));
        $chart->current = $this->dataset($user_reachups, $period, $fromDate, $toDate);

        // $chart->previous_reachups = [10,0,15,3,125,0,25,52,0,0,111,12];
        $chart->previous = $this->dataset($user_reachups, $period, $prevFrom, $prevTo);
        return response()->json($chart);
    }

    public function earningchart(Request $request)
    {
        // dd($request->all());
        // $user_reachups = UserReachup::where('status', '!=', 'pending')->where('status', '!=', 'reject')->selectRaw("count(*) data");
        $reachup_payments = UserReachup::where('status', '!=', 'pending')->where('status', '!=', 'reject')->selectRaw("sum(charges) data");

        $chart = new Chart();
        $period = isset($request->period) ? $request->period : 'Year';
        $chart->labels = $this->periodLabels[isset($request->period) ? $request->period : 'Year'] ;
        // $chart->current_reachups = [0,0,5,3,15,20,25,150,0,89,0,12];
        $prevFrom = date('Y-m-d', strtotime('first day of january previous year'));
        $prevTo = date('Y-m-d', strtotime('last day of december previous year'));
        $fromDate = date('Y-m-d', strtotime('first day of january this year'));
        $toDate = date('Y-m-d', strtotime('last day of december this year'));
        $chart->current = $this->dataset($reachup_payments, $period, $fromDate, $toDate);

        // $chart->previous_reachups = [10,0,15,3,125,0,25,52,0,0,111,12];
        $chart->previous = $this->dataset($reachup_payments, $period, $prevFrom, $prevTo);
        // dd($chart);
        // dd(json_encode($chart));
        return response()->json($chart);
    }

    public function reachupstatschart(Request $request)
    {
        // dd($request->all());
        // $user_reachups = UserReachup::where('status', '!=', 'pending')->where('status', '!=', 'reject')->selectRaw("count(*) data");
        // $reachup_stats = UserReachup::selectRaw("sum(charges) data");

        
        $chart = new Chart();
        $period = isset($request->period) ? $request->period : 'Year';
        $chart->labels = ['pending', 'process', 'completed', 'reject'];
        // $chart->labels = $this->periodLabels[isset($request->period) ? $request->period : 'Year'] ;
        // $chart->current_reachups = [0,0,5,3,15,20,25,150,0,89,0,12];
        $prevFrom = date('Y-m-d', strtotime('first day of january previous year'));
        $prevTo = date('Y-m-d', strtotime('last day of december previous year'));
        $fromDate = date('Y-m-d', strtotime('first day of january this year'));
        $toDate = date('Y-m-d', strtotime('last day of december this year'));
        $chart->current = $this->dataset('reachup_stats', $period, $fromDate, $toDate, 'doughnut');

        // $chart->previous_reachups = [10,0,15,3,125,0,25,52,0,0,111,12];
        $chart->previous = $this->dataset('reachup_stats', $period, $prevFrom, $prevTo, 'doughnut');
        return response()->json($chart);
    }

    public function dataset($qry, $period, $from, $to, $graph = 'bar')
    {
		$iterator = 12;
// dd($qry);
        if($graph != 'doughnut') {

            if ($period == 'Year') {
                $qry->selectRaw("MONTH(created_at) label")->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->groupBy(\DB::raw('MONTH(created_at)'));
                $iterator = 12;
            } elseif ($period == 'Month') {

                $qry->selectRaw("DAY(created_at) label")->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->groupBy(\DB::raw('DAY(created_at)'));
                $iterator = date("t");
            } elseif ($period == 'Quarter') {

                $qry->selectRaw("QUARTER(created_at) label")->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->groupBy(\DB::raw('QUARTER(created_at)'));
                $iterator = date("t");
            }
        }
		//\DB::enableQueryLog(); // Enable query log
        if($graph == 'doughnut') {
            if($qry == 'reachup_stats'){
                $query = "SELECT * FROM " .
                    "(" .
                    "SELECT count(*) as 'pending' FROM user_reachups " .
                    "where status = 'pending' " .
                    "and created_at between '" . $from . "' and '" . $to . "'" .
                    ") a, " .
                    "(" .
                    "SELECT count(*) as 'process' FROM user_reachups " .
                    "where status = 'process' " .
                    "and created_at between '" . $from . "' and '" . $to . "'" .
                    ") b, ".
                    "(" .
                    "SELECT count(*) as 'completed' FROM user_reachups " .
                    "where status = 'completed' " .
                    "and created_at between '" . $from . "' and '" . $to . "'" .
                    ") c, ".
                    "(" .
                    "SELECT count(*) as 'reject' FROM user_reachups " .
                    "where status = 'reject' " .
                    "and created_at between '" . $from . "' and '" . $to . "'" .
                    ") d";

            }
            // dd($qry)
            $data = \DB::select($query);
        } else {

            $data = $qry->get();
        } 
		//dd(\DB::getQueryLog()); // Show results of log
        // dd($data);
        // dd($graph);
        if($graph == 'doughnut') {

            $labels =['pending', 'process', 'completed', 'reject'];


            $result = [];
            for ($i = 0; $i < count($labels); $i++) {
                $result[] = 0;
                //$result[] = $data[0][$labels[$i]];
            }
            // dd($data[0]);
            for ($i = 0; $i < count($labels); $i++) {
                //if ($i == 1)
                //dd($data[$i]->label . "," . array_search($data[$i]->label, $labels));
                $result[$i] = $data[0]->{$labels[$i]} ?? 0;
            }
            // dd($result);
            return $result;
        } else {
            if (count($data) > 0) {
                $data = array_column($data->toArray(), 'data', 'label');
            }

            $result = [];
            for ($i = 1; $i <= $iterator; $i++) {
                $result[] = isset($data[$i]) ? ceil($data[$i]) : 0;
            }
            return $result;

        }
    }

}
