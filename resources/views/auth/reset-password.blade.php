<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password | SKK Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans flex items-center justify-center min-h-screen">

    <div class="max-w-md w-full mx-4 bg-white p-8 rounded-xl shadow-md border border-slate-200">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Set New Password</h1>
            <p class="text-sm text-slate-500 mt-1">Please configure your new secure account password below.</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-3 rounded mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ request()->email ?? $email }}">

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">New Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    autofocus
                    placeholder="Minimum 8 characters"
                    class="w-full mt-1 p-2.5 border border-slate-300 rounded-md focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition"
                >
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm New Password</label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required 
                    placeholder="Repeat your new password"
                    class="w-full mt-1 p-2.5 border border-slate-300 rounded-md focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition"
                >
            </div>

            <div>
                <button 
                    type="submit" 
                    class="w-full bg-teal-600 text-white p-2.5 rounded-md font-semibold hover:bg-teal-700 active:bg-teal-800 transition shadow-sm"
                >
                    Update Account Password
                </button>
            </div>
        </form>

        <div class="text-center mt-6 border-t border-slate-100 pt-4">
            <a href="/login" class="text-sm font-medium text-teal-600 hover:text-teal-700 transition">
                &larr; Return to Login Screen
            </a>
        </div>
    </div>

</body>
</html>