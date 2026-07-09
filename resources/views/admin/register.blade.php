<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register New Admin</title>
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
                    <i class="fa-solid fa-file-import w-6"></i> <span>Import Excel</span>
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
                <h1 class="text-xl font-semibold text-gray-800">Register New User</h1>
            </header>

            <div class="p-8 flex justify-center">
                <div class="w-full max-w-lg bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-indigo-600 p-4 text-white font-bold text-center">
                        User Details
                    </div>
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                            <p class="font-bold">Please fix the following errors:</p>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('register') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" 
                                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('name') border-red-500 @enderror" 
                                   placeholder="John Doe" required>
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" 
                                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('email') border-red-500 @enderror" 
                                   placeholder="email@example.com" required>
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <input type="password" name="password" 
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none" 
                                       required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                <input type="password" name="password_confirmation" 
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none" 
                                       required>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="role" value="superadmin" id="is_admin" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <label for="is_admin" class="ml-2 block text-sm text-gray-900">
                                Give super Admin Privileges
                            </label>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700 transition duration-300 flex items-center justify-center">
                                <i class="fa-solid fa-save mr-2"></i> Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

</body>
</html>