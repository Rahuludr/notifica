<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Operational Reporting Suite</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800">

    <div class="flex h-screen overflow-hidden">
        <aside class="w-64 bg-indigo-900 text-white flex-shrink-0 hidden md:flex flex-col">
            <div class="p-6 text-2xl font-bold border-b border-indigo-800">
                AdminPanel
            </div>
            <nav class="flex-1 mt-6 px-4 space-y-2">
                <a href="#" class="flex items-center p-3 rounded-lg hover:bg-indigo-800 transition bg-indigo-700">
                    <i class="fa-solid fa-gauge w-6"></i> <span>Dashboard</span>
                </a>
                <a href="/reports" class="flex items-center p-3 rounded-lg hover:bg-indigo-800 transition">
                    <i class="fa-solid fa-user-plus w-6"></i> <span>Reports</span>
                </a>
               
                 @if(auth()->check() && auth()->user()->isSuperAdmin())  
                <a href="/admin/register-member" class="flex items-center p-3 rounded-lg hover:bg-indigo-800 transition">
                    <i class="fa-solid fa-user-plus w-6"></i> <span>Register User</span>
                </a>

                <a href="/admin/import" class="flex items-center p-3 rounded-lg hover:bg-indigo-800 transition">
                    <i class="fa-solid fa-file-import w-6"></i> <span>Upload daily report</span>
                </a>
             @endif
            </nav>
            <div class="p-4 border-t border-indigo-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center text-indigo-300 hover:text-white transition">
                        <i class="fa-solid fa-right-from-bracket w-6"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

    <main class="flex-1 overflow-y-auto">
    <div class="max-w-7xl mx-auto px-4 py-8 space-y-6">

        <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-6">
            <form method="GET" action="{{ route('reports.index') }}" id="filter-form" class="space-y-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 tracking-tight">Reports Console</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Filter records, analyze trends, and audit raw database transactional logs.</p>
                    </div>
                    <div class="flex items-center gap-2">
                    <label for="mines-site-input" class="text-xs font-bold text-gray-700 uppercase tracking-wide">Site:</label>
                    <select name="mines_site" id="mines-site-input" class="text-xs font-semibold bg-gray-100 text-gray-900 border border-transparent rounded-lg p-2 focus:bg-white focus:ring-1 focus:ring-indigo-500 cursor-pointer">
                        <option value="">-- All Active Mines --</option>
                        @foreach($uniqueMineSites as $site)
                            <option value="{{ trim($site) }}" {{ trim(request('mines_site')) === trim($site) ? 'selected' : '' }}>{{ $site }}</option>
                        @endforeach
                    </select>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 bg-gray-100 p-1 rounded-lg self-start md:self-auto">
                        <button type="button" data-type="daily" class="type-btn px-2 py-2 text-xs font-semibold rounded-md transition-all {{ $filter === 'daily' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">Daily</button>
                        <button type="button" data-type="monthly" class="type-btn px-2 py-2 text-xs font-semibold rounded-md transition-all {{ $filter === 'monthly' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">Monthly</button>
                        <button type="button" data-type="yearly" class="type-btn px-2 py-2 text-xs font-semibold rounded-md transition-all {{ $filter === 'yearly' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">Yearly</button>
                        <button type="button" data-type="custom" class="type-btn px-2 py-2 text-xs font-semibold rounded-md transition-all {{ $filter === 'custom' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">Custom Range</button>
                    </div>
                </div>

                <input type="hidden" name="filter" id="filter-type-input" value="{{ $filter }}">

                <div id="custom-date-inputs" class="{{ $filter === 'custom' ? 'block' : 'hidden' }} pt-4 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 items-end animate-fade-in">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">From Date</label>
                        <input type="date" name="from_date" value="{{ $fromDate }}" class="w-full text-sm border-gray-300 rounded-lg p-2 bg-gray-50 border focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">To Date</label>
                        <input type="date" name="to_date" value="{{ $toDate }}" class="w-full text-sm border-gray-300 rounded-lg p-2 bg-gray-50 border focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <button type="submit" class="w-full md:w-auto bg-indigo-600 text-white text-xs font-semibold px-5 py-2.5 rounded-lg shadow-xs hover:bg-indigo-700 transition-colors">
                            Apply Dates
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-xs p-6 min-h-[380px] flex flex-col justify-between">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Mines Overview Performance</h4>
                    <span id="loading-badge" class="hidden text-xs bg-indigo-50 text-indigo-600 px-2 py-1 rounded font-medium animate-pulse">Syncing Charts...</span>
                </div>
                <div class="relative w-full flex-1">
                    <canvas id="masterBarChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-6 min-h-[380px] flex flex-col justify-center relative">
                <div id="line-chart-wrapper" class="w-full h-full flex flex-col hidden flex-1">
                    <h4 id="line-chart-title" class="text-sm font-bold text-gray-700 uppercase tracking-wide border-b border-gray-100 pb-3 mb-3">Site Month-to-Date Trend</h4>
                    <div class="relative w-full flex-1 max-h-[250px]">
                        <canvas id="mineMonthlyLineChart"></canvas>
                    </div>
                </div>

                <div id="line-placeholder" class="text-center p-6 text-gray-400">
                    <svg class="mx-auto h-10 w-10 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <p class="text-xs font-medium text-gray-500">Interactive Drilldown</p>
                    <p class="text-[11px] mt-1 text-gray-400 max-w-[200px] mx-auto">Click any bar column on the left to display its full monthly progress trend.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Detailed Audit Log Ledger</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs">
                        <tr>
                            <th class="px-6 py-3.5">Log Date</th>
                            <th class="px-6 py-3.5">Mines Site</th>
                            <th class="px-6 py-3.5">OP Actual / Target</th>
                            <th class="px-6 py-3.5">MD Actual / Target</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-600">
                        @forelse($paginatedReports as $report)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-6 py-3.5 whitespace-nowrap text-xs text-gray-500">{{ \Carbon\Carbon::parse($report->created_at)->format('d M Y H:i') }}</td>
                                <td class="px-6 py-3.5 font-medium text-gray-900">{{ $report->Mines_Site }}</td>
                                <td class="px-6 py-3.5">
                                    <div class="font-medium text-emerald-700">{{ $report->OP_act }} / {{ $report->OP_tgt }}</div>
                                    <div class="text-[11px] text-gray-400">Achv: {{ $report->OP_tgt > 0 ? number_format(($report->OP_act / $report->OP_tgt) * 100, 2) : '0.00' }}%</div>
                                </td>
                                
                                <td class="px-6 py-3.5">
                                    <div class="font-medium text-blue-700">{{ $report->MD_act }} / {{ $report->MD_tgt }}</div>
                                    <div class="text-[11px] text-gray-400">Achv: {{ $report->MD_tgt > 0 ? number_format(($report->MD_act / $report->MD_tgt) * 100, 2) : '0.00' }}%</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400 font-medium text-sm">
                                    No database entries matched your selected filter parameters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $paginatedReports->links() }}
            </div>
        </div>
    </div>
    </main>
    </div>
    <script>
        $(document).ready(function() {
            let barChartInstance = null;
            let lineChartInstance = null;
            let barServerDataset = [];

            // Run chart query based on current active server configurations on page execution loads
            fetchChartDataAggregation();

            // Toggle active styles and trigger search submissions on filter button clicks
            $('.type-btn').on('click', function() {
                const type = $(this).data('type');
                $('#filter-type-input').val(type);
                var mines_site = $('#mines-site-input').val(); // Get current mine site selection
                if (type === 'custom') {
                    // Show custom input elements rather than immediately submitting empty values
                    $('#custom-date-inputs').removeClass('hidden');
                    $('.type-btn').removeClass('bg-white text-gray-900 shadow-xs').addClass('text-gray-600 hover:text-gray-900');
                    $(this).removeClass('text-gray-600 hover:text-gray-900').addClass('bg-white text-gray-900 shadow-xs');
                } else {
                    setTimeout(function() {          
                        $('#filter-form').submit();
                     }, 1000);
                   
                }
            });

            function fetchChartDataAggregation() {
                $('#loading-badge').removeClass('hidden');
                 var mines_site = $('#mines-site-input').val();
                $.ajax({
                    url: "{{ route('reports.chart-data') }}",
                    type: "GET",
                    data: {
                        filter: "{{ $filter }}",
                        from_date: "{{ $fromDate }}",
                        to_date: "{{ $toDate }}",
                        mines_site: mines_site
                    },
                    dataType: "json",
                    success: function(response) {
                        $('#loading-badge').addClass('hidden');
                        barServerDataset = response;
                        renderBarChart(response);
                    }
                });
            }

            function renderBarChart(data) {
                const ctx = document.getElementById('masterBarChart').getContext('2d');
                if(barChartInstance !== null) barChartInstance.destroy();

                barChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(i => i.Mines_Site),
                        datasets: [
                            { label: 'Avg OP (%)', data: data.map(i => i.avg_op_percentage ? parseFloat(i.avg_op_percentage).toFixed(2) : 0), backgroundColor: '#10b981', borderRadius: 4 },
                            { label: 'Avg MD (%)', data: data.map(i => i.avg_md_percentage ? parseFloat(i.avg_md_percentage).toFixed(2) : 0), backgroundColor: '#3b82f6', borderRadius: 4 }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true, ticks: { callback: v => v + '%' } } },
                        onClick: (event, elements) => {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const selectedSite = barServerDataset[index].Mines_Site;
                                fetchMineMonthlyTrend(selectedSite);
                            }
                        }
                    }
                });
            }

            function fetchMineMonthlyTrend(siteName) {
                $('#loading-badge').removeClass('hidden');

                $.ajax({
                    url: "{{ route('reports.mine-trend') }}",
                    type: "GET",
                    data: { mine_site: siteName },
                    dataType: "json",
                    success: function(response) {
                        $('#loading-badge').addClass('hidden');
                        $('#line-placeholder').addClass('hidden');
                        $('#line-chart-wrapper').removeClass('hidden');
                        $('#line-chart-title').text(`${response.mine_site} - Monthly Performance Trend`);
                        
                        renderLineChart(response.trend);
                    }
                });
            }

            function renderLineChart(trendData) {
                const ctx = document.getElementById('mineMonthlyLineChart').getContext('2d');
                if(lineChartInstance !== null) lineChartInstance.destroy();

                lineChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: trendData.map(i => i.date_label),
                        datasets: [
                            { label: 'Daily OP (%)', data: trendData.map(i => parseFloat(i.daily_op_percentage).toFixed(2)), borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.03)', fill: true, tension: 0.15, borderWidth: 2.5 },
                            { label: 'Daily MD (%)', data: trendData.map(i => parseFloat(i.daily_md_percentage).toFixed(2)), borderColor: '#3b82f6', backgroundColor: 'rgba(59, 130, 246, 0.03)', fill: true, tension: 0.15, borderWidth: 2.5 }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        scales: { y: { beginAtZero: true, ticks: { callback: v => v + '%' } } }
                    }
                });
            }
        });
    </script>
</body>
</html>