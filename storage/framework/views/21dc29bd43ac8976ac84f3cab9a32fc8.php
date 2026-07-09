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
    <div class="bg-white border border-gray-200 shadow-2xl rounded-xl overflow-hidden flex flex-col h-full drag-region">
        <div class="bg-slate-800 text-white p-6 rounded-lg shadow-md mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-wide">S K KHETAN GROUP</h1>
                <p class="text-xs text-slate-400 mt-1">Zero Harm • Safe Productivity • Performance Excellence</p>
            </div>
            <div class="text-right">
                <span class="bg-teal-500 text-xs text-white font-bold px-3 py-1 rounded-full uppercase">Operational Dashboard</span>
                <p class="text-sm text-slate-300 mt-1">Data Date: 12.04.2026</p>
            </div>
        </div> 
        <div class="bg-blue-600 p-3 text-white flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-lg">📊</span>
                <h3 class="font-bold text-sm uppercase tracking-wider">Reports</h3>
            </div>
            <button onclick="window.close()" class="no-drag hover:bg-blue-700 px-2 rounded text-xl">&times;</button>
        </div>

        <div class="p-4 flex-1 flex flex-col no-drag">
            <p class="text-gray-600 text-xs mb-2">Mining data trend for the last batch:</p>
            
            <div class="flex-1 min-h-0">
                <canvas id="notifyChart"></canvas>
            </div>
        </div>



        <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col md:flex-row min-h-[600px]">
            
            <div class="w-full md:w-1/3 bg-slate-50 border-r border-slate-200 flex flex-col">
                <div class="bg-slate-800 text-white p-4">
                    <h2 class="font-bold text-lg">Mine Sites</h2>
                    <p class="text-xs text-slate-300">Select a site to view its cumulative graph</p>
                </div>
                <div class="overflow-y-auto flex-1 p-2" id="site-list">
                    </div>
            </div>

            <div class="w-full md:w-2/3 p-6 flex flex-col">
                <div class="mb-4">
                    <h2 id="chart-title" class="text-2xl font-bold text-slate-800">Select a Mine Site</h2>
                    <p class="text-sm text-slate-500">Showing cumulative OP_act for June</p>
                </div>
                
                <div class="flex-1 relative w-full h-[400px]">
                    <canvas id="siteChart"></canvas>
                </div>
            </div>

        </div>
    </div>
    

        <div>
    <h1>Imported Reports (<?php echo e(count($reports['reports'])); ?>)</h1>
       <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b-2 border-slate-100">
                            <th class="py-3 px-2 text-slate-600 font-semibold">Mines Site</th>
                            <th class="py-3 px-2 text-slate-600 font-semibold">LTI/MTI Act</th>
                            <th class="py-3 px-2 text-slate-600 font-semibold">FAC Act</th>
                            <th class="py-3 px-2 text-slate-600 font-semibold">OP Tgt (m)/ OP Act (m) </th>
                            <th class="py-3 px-2 text-slate-600 font-semibold text-right">OP ACHV</th>
                             <th class="py-3 px-2 text-slate-600 font-semibold">MD Tgt (m) / MD Act (m)</th>
                            <th class="py-3 px-2 text-slate-600 font-semibold">MD Achv %</th>
                            <th class="py-3 px-2 text-slate-600 font-semibold">Date</th>
                        </tr>
                    </thead>
                  <tbody>
                        <?php $__currentLoopData = $reports['reports']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                     

                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="py-2 px-2 font-medium text-slate-800"><?php echo e($report['Mines_Site'] ?? 'No Title'); ?></td>
                            <td class="py-2 px-2 font-medium text-slate-800"><?php echo e($report['LTI_MTI_act'] ?? 0); ?></td>
                            <td class="py-2 px-2 font-medium text-slate-800"><?php echo e($report['FAC_act'] ?? 0); ?></td>
                            <td class="py-2 px-2 font-medium text-slate-800"><?php echo e($report['OP_tgt'] ?? 0); ?> / <?php echo e($report['OP_act'] ?? 0); ?></td>
                            <td class="py-2 px-2 text-right text-slate-600 font-mono">
                                  <?php if($report['OP_achv'] == 1.0 || $report['OP_achv'] >= 0.9): ?>
                                  <span class="bg-green-100 text-green-700 px-2 py-1  font-bold"><?php echo e(number_format(((float)($report['OP_achv'] ?? 0) * 100), 1)); ?>%</span>
                                  <?php elseif($report['OP_achv'] <= 0.9 || $report['OP_achv'] >= 0.51): ?>
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1  font-bold"><?php echo e(number_format(((float)($report['OP_achv'] ?? 0) * 100), 1)); ?>%</span>
                                    <?php elseif($report['OP_achv'] <= 0.50 || $report['OP_achv'] >= 0.25): ?>
                                    <span class="bg-orange-100 text-orange-700 px-2 py-1  font-bold"><?php echo e(number_format(((float)($report['OP_achv'] ?? 0) * 100), 1)); ?>%</span>
                                     <?php elseif($report['OP_achv'] <= 0.25): ?>
                                    <span class="bg-red-100 text-red-700 px-2 py-1  font-bold"><?php echo e(number_format(((float)($report['OP_achv'] ?? 0) * 100), 1)); ?>%</span>
                                    <?php endif; ?>
                            </td>
                            <td class="py-2 px-2 font-medium text-slate-800"><?php echo e($report['MD_tgt'] ?? 0); ?> / <?php echo e($report['MD_act'] ?? 0); ?></td>
                            <td class="py-2 px-2 font-medium text-slate-800"><?php echo e($report['MD_achv'] ?? 0); ?></td>
                            <td class="py-2 px-2 font-medium text-slate-800"><?php echo e($report['created_at']); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
</div>

    </div>

    <script>
        const ctx = document.getElementById('notifyChart').getContext('2d');
        const data = <?php echo json_encode($reports['reports'] ?? [], 15, 512) ?>;

        function viewSiteTrend(siteName) {
            if (!siteName) return;
            
            // Check if running inside Electron context safely
            if (window.electron && window.electron.send) {
                window.electron.send('open-mine-trend', siteName);
            } else if (window.ipcRenderer) {
                window.ipcRenderer.send('open-mine-trend', siteName);
            } else {
                // Fallback for native web browser testing environment
                console.log(`Fallback redirection logic for browser. Selected: ${siteName}`);
                window.location.href = `/reports?filter=custom&mine_site=${encodeURIComponent(siteName)}`;
            }
        }
        
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
                        viewSiteTrend(selectedSite);
                    }
                }
            }
        });



               const rawData = <?php echo json_encode($reports['cumulative'] ?? [], 15, 512) ?>;
     
                // 2. State Management
        let currentChart = null; // Holds the Chart instance
        const ctx2 = document.getElementById('siteChart').getContext('2d');
        const siteListContainer = document.getElementById('site-list');
        const chartTitle = document.getElementById('chart-title');

        // 3. Render the list of buttons
        function buildSiteList() {
            rawData.datasets.forEach(dataset => {
                const btn = document.createElement('button');
                btn.className = "w-full text-left px-4 py-3 mb-1 rounded bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-300 font-medium text-slate-700 transition-colors";
                btn.innerText = dataset.name;
                
                btn.onclick = (e) => {
                    // Highlight active button
                    document.querySelectorAll('#site-list button').forEach(b => {
                        b.classList.remove('bg-blue-600', 'text-white', 'hover:bg-blue-600');
                        b.classList.add('bg-white', 'text-slate-700');
                    });
                    e.target.classList.remove('bg-white', 'text-slate-700', 'hover:bg-blue-50');
                    e.target.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-600');

                    // Draw the chart for this site
                    drawChart(dataset.name);
                };

                siteListContainer.appendChild(btn);
            });
        }

        // 4. Draw/Update the Chart
        function drawChart(siteName) {
            // Find the data for the clicked site
            const siteData = rawData.datasets.find(d => d.name === siteName);
            if (!siteData) return;

            // Update title
            chartTitle.innerText = `Cumulative Data: ${siteName}`;

            // Destroy existing chart if it exists so we can draw a new one
            if (currentChart) {
                currentChart.destroy();
            }

            // Create new line chart
            currentChart = new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: rawData.labels,
                    datasets: [{
                        label: `${siteName} Production`,
                        data: siteData.data,
                        borderColor: '#2563eb', // Tailwind blue-600
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#1e40af', // Tailwind blue-800
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.3 // Smooth curves
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' OP_act';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e2e8f0' } // Tailwind slate-200
                        }
                    }
                }
            });
        }

        // Initialize: Build the list and automatically click the first item to show a default graph
        document.addEventListener("DOMContentLoaded", () => {
            buildSiteList();
            
            // Auto-click the first button if it exists
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
</html><?php /**PATH /var/www/html/desktopnotification/resources/views/native/import-notification.blade.php ENDPATH**/ ?>