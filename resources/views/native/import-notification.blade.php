<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { margin: 0; padding: 0; overflow: visible; background: transparent; }
        .drag-region { -webkit-app-region: drag; } /* Allows moving the popup */
        .no-drag { -webkit-app-region: no-drag; }
    </style>
</head>
<body class="p-2"> 
   <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 space-y-6 bg-slate-50 min-h-screen font-sans drag-region">

    <div class="bg-slate-800 text-white p-6 rounded-xl shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-drag">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-wide">S K KHETAN GROUP</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1 uppercase tracking-wider">Zero Harm • Safe Productivity • Performance Excellence</p>
        </div>
        <div class="text-left md:text-right">
            <span class="bg-teal-500 text-xs text-white font-bold px-3 py-1 rounded-full uppercase tracking-wide shadow-sm">Operational Dashboard</span>
            @php 
                $mytime = Carbon\Carbon::now();
            @endphp
            <p class="text-sm text-slate-300 mt-2 font-mono">Data Date: {{ $mytime->format('d-m-Y');  }}</p>
        </div>
    </div> 

    <div class="bg-white border border-slate-200 shadow-md rounded-xl overflow-hidden flex flex-col no-drag">
        <div class="bg-slate-100 text-slate-700 p-4 border-b border-slate-200 font-bold uppercase text-sm tracking-wider flex items-center gap-2">
            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            Yesterday Performance Metrics
        </div>
        <div class="p-4 w-full h-64 md:h-80 relative">
            <canvas id="notifyChart"></canvas>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-xl overflow-hidden flex flex-col no-drag">
        <div class="bg-slate-800 text-white p-6 border-b border-slate-700">
            <h2 class="text-xl sm:text-2xl font-bold tracking-wide">Cumulative Performance Dashboard</h2>
            <p class="text-sm text-slate-300 mt-1">Compare Target vs Actual for OP and MD</p>
        </div>

        <div class="flex flex-col md:flex-row min-h-[600px]">
            <div class="w-full md:w-1/4 lg:w-1/5 bg-slate-50 border-b md:border-b-0 md:border-r border-slate-200 flex flex-col">
                <div class="bg-slate-200 text-slate-700 p-3 border-b border-slate-300 font-bold uppercase text-xs tracking-wider">
                    Select Mine Site
                </div>
                <div class="overflow-y-auto flex-1 p-3 space-y-2 max-h-48 md:max-h-none" id="site-list">
                    </div>
            </div>

            <div class="w-full md:w-3/4 lg:w-4/5 p-4 sm:p-6 relative flex flex-col bg-white">
                
                <div id="loading-overlay" class="absolute inset-0 bg-white/90 backdrop-blur-sm z-10 flex flex-col items-center justify-center hidden rounded-br-xl">
                    <div class="w-10 h-10 border-4 border-slate-200 border-t-blue-600 rounded-full animate-spin mb-4"></div>
                    <p class="text-slate-600 font-medium animate-pulse">Loading site data...</p>
                </div>

                <div class="mb-6 pb-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-2">
                    <div>
                        <h2 id="current-site-title" class="text-2xl sm:text-3xl font-bold text-slate-800">Site Name</h2>
                        <p class="text-sm text-slate-500 font-medium mt-1">Cumulative Metrics</p>
                    </div>
                </div>
                
                <div id="charts-container" class="flex-1 grid grid-cols-1 lg:grid-cols-2 gap-6 transition-opacity duration-300">
                    <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl flex flex-col min-h-[300px]">
                        <h3 class="text-md sm:text-lg font-semibold text-slate-700 mb-4 flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-600 shadow-sm"></span> Ore Production (OP)
                        </h3>
                        <div class="flex-1 relative w-full">
                            <canvas id="opChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl flex flex-col min-h-[300px]">
                        <h3 class="text-md sm:text-lg font-semibold text-slate-700 mb-4 flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-600 shadow-sm"></span> Mine Development (MD)
                        </h3>
                        <div class="flex-1 relative w-full">
                            <canvas id="mdChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-xl overflow-hidden no-drag">
        <div class="bg-slate-800 text-white p-6 flex justify-between items-center">
            <h2 class="text-xl sm:text-2xl font-bold tracking-wide">Yesterday Reports</h2>
            <span class="bg-slate-600 text-white py-1 px-3 rounded-full text-sm font-bold">{{ count($reports['reports'] ?? []) }} Records</span>
        </div>
        
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b-2 border-slate-200">
                        <th class="py-4 px-4 text-slate-600 font-semibold text-sm uppercase tracking-wider">Mines Site</th>
                        <th class="py-4 px-4 text-slate-600 font-semibold text-sm uppercase tracking-wider text-center">LTI/MTI Act</th>
                        <th class="py-4 px-4 text-slate-600 font-semibold text-sm uppercase tracking-wider text-center">FAC Act</th>
                        <th class="py-4 px-4 text-slate-600 font-semibold text-sm uppercase tracking-wider text-center">OP Tgt / Act (m)</th>
                        <th class="py-4 px-4 text-slate-600 font-semibold text-sm uppercase tracking-wider text-right">OP ACHV</th>
                        <th class="py-4 px-4 text-slate-600 font-semibold text-sm uppercase tracking-wider text-center">MD Tgt / Act (m)</th>
                        <th class="py-4 px-4 text-slate-600 font-semibold text-sm uppercase tracking-wider text-right">MD Achv %</th>
                        <th class="py-4 px-4 text-slate-600 font-semibold text-sm uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($reports['reports'] ?? [] as $report)
                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="py-3 px-4 font-semibold text-slate-800">{{ $report['Mines_Site'] ?? 'No Title' }}</td>
                        <td class="py-3 px-4 font-medium text-slate-600 text-center">{{ $report['LTI_MTI_act'] ?? 0 }}</td>
                        <td class="py-3 px-4 font-medium text-slate-600 text-center">{{ $report['FAC_act'] ?? 0 }}</td>
                        <td class="py-3 px-4 font-medium text-slate-800 text-center">{{ $report['OP_tgt'] ?? 0 }} <span class="text-slate-400 mx-1">/</span> {{ $report['OP_act'] ?? 0 }}</td>
                        <td class="py-3 px-4 text-right">
                            @if(($report['OP_achv'] ?? 0) >= 0.9)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold tracking-wide">{{ number_format(((float)($report['OP_achv'] ?? 0) * 100), 1) }}%</span>
                            @elseif(($report['OP_achv'] ?? 0) >= 0.51)
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold tracking-wide">{{ number_format(((float)($report['OP_achv'] ?? 0) * 100), 1) }}%</span>
                            @elseif(($report['OP_achv'] ?? 0) >= 0.25)
                                <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold tracking-wide">{{ number_format(((float)($report['OP_achv'] ?? 0) * 100), 1) }}%</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold tracking-wide">{{ number_format(((float)($report['OP_achv'] ?? 0) * 100), 1) }}%</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-800 text-center">{{ $report['MD_tgt'] ?? 0 }} <span class="text-slate-400 mx-1">/</span> {{ $report['MD_act'] ?? 0 }}</td>
                         <td class="py-3 px-4 text-right"> @if(($report['MD_achv'] ?? 0) >= 0.9)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold tracking-wide">{{ number_format(((float)($report['MD_achv'] ?? 0) * 100), 1) }}%</span>
                            @elseif(($report['MD_achv'] ?? 0) >= 0.51)
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold tracking-wide">{{ number_format(((float)($report['MD_achv'] ?? 0) * 100), 1) }}%</span>
                            @elseif(($report['MD_achv'] ?? 0) >= 0.25)
                                <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold tracking-wide">{{ number_format(((float)($report['MD_achv'] ?? 0) * 100), 1) }}%</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold tracking-wide">{{ number_format(((float)($report['MD_achv'] ?? 0) * 100), 1) }}%</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-500 text-sm">{{ \Carbon\Carbon::parse($report['created_at'])->setTimezone('Asia/Kolkata')->format('d-m-Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

    <script>
        const ctx = document.getElementById('notifyChart').getContext('2d');
        const data = @json($reports['reports'] ?? []);

      
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(r => r.Mines_Site),
                datasets: [{
                    label: 'Performance',
                    data: data.map(r => r.OP_achv*100),
                    backgroundColor: data.map(r => {
                        if (r.OP_achv >= 1)    return '#16a34a'; // Green (100%+)
                        if (r.OP_achv > 0.50)  return '#2563eb'; // Blue (51% - 99%)
                        if (r.OP_achv > 0.10)  return '#ea580c'; // Orange (11% - 50%)
                        return '#f92b07';                        // Red (0% - 10%)
                    }),
                    borderRadius: 5
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, max: 100 } },
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const selectedSite = data[index].Mines_Site;
                    }
                }
            }
        });

// 1. Listen for the lightweight ping
        let parsedCumulativeData = null;
let cumulativeData = null;
let opChartInstance = null;
let mdChartInstance = null;

const siteListContainer = document.getElementById("site-list");
const currentSiteTitle = document.getElementById("current-site-title");
const loadingOverlay = document.getElementById("loading-overlay");

const ctxOp = document.getElementById("opChart").getContext("2d");
const ctxMd = document.getElementById("mdChart").getContext("2d");

const commonChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: {
        mode: "index",
        intersect: false
    },
    plugins: {
        legend: {
            position: "top"
        }
    },
    scales: {
        y: {
            beginAtZero: true
        }
    }
};

fetch("http://notifica.com/api/latest-reports")
    .then(res => res.json())
    .then(data => {

        console.log(data);

        const parsed = JSON.parse(data.cumulative);

        cumulativeData = parsed.cumulative;

        console.log(cumulativeData);

        buildSiteList();

        if (cumulativeData.datasets.length) {
            loadSiteData(cumulativeData.datasets[0].name);

            const firstBtn = document.querySelector("#site-list button");

            if (firstBtn) {
                firstBtn.classList.remove("bg-white");
                firstBtn.classList.add("bg-slate-800","text-white");
            }
        }

    })
    .catch(console.error);

function buildSiteList() {

    siteListContainer.innerHTML = "";

    cumulativeData.datasets.forEach(site => {

        const btn = document.createElement("button");

        btn.className =
            "w-full text-left px-4 py-3 rounded-lg bg-white border border-slate-200 hover:bg-slate-100";

        btn.innerHTML = site.name;

        btn.onclick = function () {

            document.querySelectorAll("#site-list button").forEach(b => {
                b.classList.remove("bg-slate-800","text-white");
                b.classList.add("bg-white");
            });

            btn.classList.remove("bg-white");
            btn.classList.add("bg-slate-800","text-white");

            loadSiteData(site.name);

        };

        siteListContainer.appendChild(btn);

    });

}

function loadSiteData(siteName) {

    const site = cumulativeData.datasets.find(x => x.name === siteName);

    if (!site) return;

    currentSiteTitle.innerHTML = site.name;

    renderCharts(site);

}

function renderCharts(site) {

    if(opChartInstance) opChartInstance.destroy();
    if(mdChartInstance) mdChartInstance.destroy();

    opChartInstance = new Chart(ctxOp,{
        type:"line",
        data:{
            labels:cumulativeData.labels,
            datasets:[
                {
                    label:"Target",
                    data:site.data_OP_tgt,
                    borderColor:"#94a3b8",
                    borderDash:[5,5]
                },
                {
                    label:"Actual",
                    data:site.data_OP_act,
                    borderColor:"#2563eb",
                    tension:.3,
                    fill:false
                }
            ]
        },
        options:commonChartOptions
    });

    mdChartInstance = new Chart(ctxMd,{
        type:"line",
        data:{
            labels:cumulativeData.labels,
            datasets:[
                {
                    label:"Target",
                    data:site.data_MD_tgt,
                    borderColor:"#94a3b8",
                    borderDash:[5,5]
                },
                {
                    label:"Actual",
                    data:site.data_MD_act,
                    borderColor:"#16a34a",
                    tension:.3,
                    fill:false
                }
            ]
        },
        options:commonChartOptions
    });

}
        document.addEventListener("DOMContentLoaded", () => {
            buildSiteList();
            
            const firstButton = siteListContainer.querySelector('button');
            if(firstButton) {
                firstButton.click();
            }
        });
  

        window.moveTo(window.screen.availWidth - 370, window.screen.availHeight - 270);
        // Auto-close after 15 seconds
      //  setTimeout(() => window.close(), 15000);
    </script>
</body>
</html>