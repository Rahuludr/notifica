<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">
        <aside class="w-64 bg-indigo-900 text-white flex-shrink-0 hidden md:flex flex-col">
            <div class="p-6 text-2xl font-bold border-b border-indigo-800">AdminPanel</div>
            <nav class="flex-1 mt-6 px-4 space-y-2">
                <a href="/dashboard" class="flex items-center p-3 rounded-lg hover:bg-indigo-800 transition">
                    <i class="fa-solid fa-gauge w-6"></i> <span>Dashboard</span>
                </a>
                <a href="/reports" class="flex items-center p-3 rounded-lg hover:bg-indigo-800 transition">
                    <i class="fa-solid fa-user-plus w-6"></i> <span>Reports</span>
                </a>
                @if(auth()->check() && auth()->user()->isSuperAdmin())           
       
                <a href="/admin/register-member" class="flex items-center p-3 rounded-lg bg-indigo-700">
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
            <header class="bg-white shadow-sm p-4 flex justify-between items-center">
                <h1 class="text-xl font-semibold text-gray-800">Report Section</h1>
            </header>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fa-solid fa-file-invoice text-indigo-600 mr-2"></i> Report Section Upload
            </h3>
            @if(session('success'))
            <div class="mb-6 flex items-center p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg shadow-sm">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-bold text-green-800">Success!</p>
                <p class="text-xs text-green-700">{{ session('success') }}</p>
            </div>
            </div>
            @endif

            @if($errors->any() || session('error'))
            <div class="mb-6 flex items-center p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-triangle-exclamation text-red-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-bold text-red-800">Import Failed</p>
                <ul class="mt-1 list-disc list-inside text-xs text-red-700">
                    @if(session('error'))
                        <li>{{ session('error') }}</li>
                    @endif
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            </div>
            @endif
            <form action="{{ route('reports.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex items-center space-x-4">
                    <input type="file" name="file" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                        Upload Report
                    </button>
                </div>
            </form>
            </div>

        </main>
    </div>

</body>
</html>