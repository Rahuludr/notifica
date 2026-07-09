<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Events\ReportImported;
use Native\Laravel\Facades\Window;
use App\Models\Report;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function showImportForm() { return view('admin.import'); }
    
    public function getMonthlyCumulativeData()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth(); 
    
        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);
        $allDates = [];
        $labels = []; 
        
        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $allDates[] = $dateString;
            $labels[] = $date->format('M d'); 
        }

        $records = DB::table('reports') 
            ->selectRaw('
                Mines_Site, 
                DATE(created_at) as date, 
                SUM(OP_tgt) as daily_op_tgt,
                 SUM(OP_act) as daily_op_act,
                 SUM(MD_tgt) as daily_md_tgt,
                 SUM(MD_act) as daily_md_act
            ')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->whereNotNull('Mines_Site') 
            ->groupBy('Mines_Site', DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $groupedBySite = $records->groupBy('Mines_Site');
        $datasets = [];

        foreach ($groupedBySite as $siteName => $siteRecords) {
            $recordsByDate = $siteRecords->keyBy('date');
            
            $cumulativeData_OP_tgt = [];
            $cumulativeData_OP_act = [];
            $cumulativeData_MD_tgt = [];
            $cumulativeData_MD_act = [];
            $runningTotaltgt = 0;
            $runningTotalact = 0;
            $runningTotalmdtgt = 0;
            $runningTotalmdact = 0;

            foreach ($allDates as $dateString) {
                if ($recordsByDate->has($dateString)) {
                     $runningTotaltgt += $recordsByDate->get($dateString)->daily_op_tgt;
                    $runningTotalact += $recordsByDate->get($dateString)->daily_op_act;
                    $runningTotalmdtgt += $recordsByDate->get($dateString)->daily_md_tgt;
                    $runningTotalmdact += $recordsByDate->get($dateString)->daily_md_act;
                }
                $cumulativeData_OP_act[] = $runningTotalact;
                $cumulativeData_OP_tgt[] = $runningTotaltgt; 
                $cumulativeData_MD_act[] = $runningTotalmdact;
                $cumulativeData_MD_tgt[] = $runningTotalmdtgt;
            }
            
            $datasets[] = [
                'name' => $siteName,
                'data_OP_tgt' => $cumulativeData_OP_tgt,
                'data_OP_act' => $cumulativeData_OP_act,
                'data_MD_tgt' => $cumulativeData_MD_tgt,
                'data_MD_act' => $cumulativeData_MD_act
            ];
        }

        // Return as a standard array, NOT response()->json()
        return [
            'cumulative' => [
                'labels' => $labels,
                'datasets' => $datasets
            ]
        ];
    }

    public function importReport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $fileName = $timestamp . '_' ."report.xlsx";
        $file->storeAs('imports', $fileName, 'public');
        $fullPath = Storage::disk('public')->path('imports/' . $fileName);

       

            try {
                \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ReportsImport, $fullPath);            
            
                try {    
                $latestDate = Report::max('created_at');      
                if ($latestDate) {
                $latest = Carbon::parse($latestDate);

                /*  $reports = Report::where('created_at', '>=', $latest->copy()->startOfMinute())
                    ->where('created_at', '<=', $latest)
                    ->orderBy('created_at', 'asc')
                    ->get();*/

                    $reports = Report::latest()
                    ->limit(10)
                    ->get();

                    $reportData['reports'] = $reports->toArray();

                    if (count($reportData) > 0) {
                        $cumulative = json_encode($this->getMonthlyCumulativeData());
                       //  $reportData = array_merge($reportData, ['cumulative' => $cumulative]);
                        //    echo '<pre/>';print_r($reportData); die;
                         broadcast(new ReportImported(
                        'Mining Report Update', 
                        'Reports from the last minute are ready.',
                        $reportData
                    ))->toOthers();
                    }
                }
                } catch (\Exception $e) {
                    \Log::error("Native Window Failed: " . $e->getMessage());
                }
            return back()->with('success', 'Specific report imported!');
            } catch (\Exception $e) {
                return back()->withErrors(['file' => 'Import failed: ' . $e->getMessage()]);
            }
        }


// app/Http\Controllers\ReportController.php for Api

        public function getLatestReports()
        {
            $reports = Report::latest()
                            ->limit(10)
                            ->get();

            $reportData['reports'] = $reports->toArray();
            if (count($reportData) > 0) {
                $cumulative = json_encode($this->getMonthlyCumulativeData());
                $reportData = array_merge($reportData, ['cumulative' => $cumulative]);
            }
            return response()->json($reportData);
        }

        public function getLatestReportData()
        {
            // 1. Find the latest date present in the table
            $latestDate = Report::max('created_at'); 

            if (!$latestDate) {
                return [
                    'labels' => [],
                    'values' => [],
                    'count'  => 0
                ];
            }

        // 2. Fetch all records from that specific day
        $reports = Report::whereDate('created_at', '=', date('Y-m-d', strtotime($latestDate)))
            ->orderBy('created_at', 'asc')
            ->get();

        // 3. Format data for your Blade Chart
        return [
            'labels' => $reports->pluck('id')->map(fn($id) => "ID: $id")->toArray(),
            'values' => $reports->pluck('efficiency')->toArray(), // Replace 'efficiency' with your numeric column
            'count'  => $reports->count(),
            'date'   => date('Y-m-d', strtotime($latestDate))
        ];
    }

   public function index(Request $request)
    {
        // Set the default filter to 'daily' if not specified
        $filter = $request->get('filter', 'daily');
        $minesSite = $request->get('mines_site');

        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $uniqueMineSites = Report::whereNotNull('Mines_Site')
                             ->distinct()
                             ->pluck('Mines_Site');

        // Base Query for the Paginated Data Table
        $tableQuery = Report::query();
        
        if ($request->filled('mines_site')) {
        $tableQuery->where('Mines_Site', trim($request->mines_site));
        }
        // Apply Time Constraints to Table base query
        $this->applyTimeFilters($tableQuery, $filter, $fromDate, $toDate);

        // Fetch paginated records (10 per page) and append query strings to keep pagination links working
        $paginatedReports = $tableQuery->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.report_index', compact('uniqueMineSites', 'paginatedReports', 'filter', 'fromDate', 'toDate'));
    }

    public function getChartData(Request $request)
    {
        $filter = $request->get('filter', 'daily');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $minesSite = $request->get('mines_site');

        $chartQuery = Report::query()->selectRaw('
            Mines_Site,
            (SUM(op_act) / NULLIF(SUM(op_tgt), 0)) * 100 as avg_op_percentage,
            (SUM(md_act) / NULLIF(SUM(md_tgt), 0)) * 100 as avg_md_percentage
        ');

        if ($request->filled('mines_site')) {
        $chartQuery->where('Mines_Site', trim($request->mines_site));
        }

        $this->applyTimeFilters($chartQuery, $filter, $fromDate, $toDate);

        $data = $chartQuery->groupBy('Mines_Site')->get();

        return response()->json($data);
    }

    public function getMineMonthlyTrend(Request $request)
    {
        $request->validate(['mine_site' => 'required|string']);
        $mineSite = $request->get('mine_site');

        // Always show the day-by-day breakdown of the current month for the clicked mine
        $dailyLogs = Report::where('Mines_Site', $mineSite)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->selectRaw('
                DAY(created_at) as day_num,
                DATE_FORMAT(created_at, "%d %b") as date_label,
                (SUM(op_act) / NULLIF(SUM(op_tgt), 0)) * 100 as daily_op_percentage,
                (SUM(md_act) / NULLIF(SUM(md_tgt), 0)) * 100 as daily_md_percentage
            ')
            ->groupBy('day_num', 'date_label')
            ->orderBy('day_num', 'asc')
            ->get();

        return response()->json([
            'mine_site' => $mineSite,
            'trend' => $dailyLogs
        ]);
    }

    /**
     * Helper to centralize query filtering logic
     */
    private function applyTimeFilters($query, $filter, $fromDate, $toDate)
    {
        if ($filter === 'daily') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($filter === 'monthly') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        } elseif ($filter === 'yearly') {
            $query->whereYear('created_at', now()->year);
        } elseif ($filter === 'custom' && $fromDate && $toDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($fromDate)->startOfDay(), 
                Carbon::parse($toDate)->endOfDay()
            ]);
        }
    }
}