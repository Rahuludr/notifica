<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans antialiased">

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
               
                 <?php if(auth()->check() && auth()->user()->isSuperAdmin()): ?>  
                <a href="/admin/register-member" class="flex items-center p-3 rounded-lg hover:bg-indigo-800 transition">
                    <i class="fa-solid fa-user-plus w-6"></i> <span>Register User</span>
                </a>

                <a href="/admin/import" class="flex items-center p-3 rounded-lg hover:bg-indigo-800 transition">
                    <i class="fa-solid fa-file-import w-6"></i> <span>Upload daily report</span>
                </a>
             <?php endif; ?>
            </nav>
            <div class="p-4 border-t border-indigo-800">
                <form action="<?php echo e(route('logout')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="flex items-center text-indigo-300 hover:text-white transition">
                        <i class="fa-solid fa-right-from-bracket w-6"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto">
            <header class="bg-white shadow-sm p-4 flex justify-between items-center">
                <h1 class="text-xl font-semibold text-gray-800">Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, <?php echo e(auth()->user()->name); ?></span>
                    <div class="h-10 w-10 bg-indigo-500 rounded-full flex items-center justify-center text-white font-bold">
                        <?php echo e(substr(auth()->user()->name, 0, 1)); ?>

                    </div>
                </div>
            </header>

            <div class="p-8">
                <?php if(session('success')): ?>
                    <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>

               
                <h3 class="text-lg text-center font-bold text-gray-900 tracking-tight mb-4">
                    Monthly Average Matrices
                </h3>
                <div class="flex-1 min-h-2">
                    <canvas id="notifyChart"></canvas>
                </div>
                <div class="mt-10 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 font-semibold text-gray-800">Yesterday Report</div>
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                            <tr>
                                <th class="px-6 py-3">Mines Site</th>
                                <th class="px-6 py-3">Op Achv</th>
                                <th class="px-6 py-3">MD Achv</th>
                                <th class="px-6 py-3 text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php $__currentLoopData = \App\Models\Report::latest()->take(10)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $todaysReport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50 transition">
        
         
                                <td class="px-6 py-4"><?php echo e($todaysReport->Mines_Site); ?></td>
                                <td class="px-6 py-4 text-gray-500"><?php echo e(number_format((float)$todaysReport->OP_achv * 100, 2)); ?>%</td>
                                <td class="px-6 py-4 text-gray-500"><?php echo e(number_format((float)$todaysReport->MD_achv * 100, 2)); ?>%</td>
                                <td class="px-6 py-4 text-gray-500 text-right"><?php echo e($todaysReport->created_at); ?></td>
                               
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
   <?php $monthlyMineAverages = \App\Models\Report::whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->selectRaw('
        Mines_Site,
        (SUM(op_act) / NULLIF(SUM(op_tgt), 0)) * 100 as avg_op_percentage,
        (SUM(md_act) / NULLIF(SUM(md_tgt), 0)) * 100 as avg_md_percentage
    ')
    ->groupBy('Mines_Site')
    ->get(); 
    ?>
 <script>
        const ctx = document.getElementById('notifyChart').getContext('2d');
        const rawData = <?php echo json_encode($monthlyMineAverages, 15, 512) ?>;

        // Extracting labels and datasets from the payload
        const labels = rawData.map(item => item.Mines_Site.trim());
        const opData = rawData.map(item => item.avg_op_percentage !== null ? item.avg_op_percentage.toFixed(1) : null);
        const mdData = rawData.map(item => item.avg_md_percentage !== null ? item.avg_md_percentage.toFixed(1) : null);

      
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Ore Production (OP) Average %',
                        data: opData,
                        backgroundColor: '#3b82f6', // Bright Slate Blue
                        borderColor: '#2563eb',
                        borderWidth: 1,
                        borderRadius: 4,
                        skipNull: true
                    },
                    {
                        label: 'Mine Development (MD) Average %',
                        data: mdData,
                        backgroundColor: '#10b981', // Emerald Green
                        borderColor: '#059669',
                        borderWidth: 1,
                        borderRadius: 4,
                        skipNull: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { weight: 'bold' },
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label.split(' ')[0] || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + '%';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 110, // Gives padding at the top for 100% values
                        title: {
                            display: true,
                            text: 'Achievement Percentage (%)',
                            font: { weight: 'bold' }
                        },
                        ticks: {
                            callback: function(value) { return value + '%'; }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            autoSkip: false,
                            maxRotation: 30,
                            minRotation: 30
                        }
                    }
                }
            }
        });
    

        window.moveTo(window.screen.availWidth - 370, window.screen.availHeight - 270);
        // Auto-close after 15 seconds
      //  setTimeout(() => window.close(), 15000);
    </script>
</body>
</html><?php /**PATH /var/www/html/desktopnotification/resources/views/dashboard.blade.php ENDPATH**/ ?>