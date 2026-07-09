<<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password | SKK Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans flex items-center justify-center min-h-screen">

   <div class="max-w-md mx-auto my-10 bg-white p-8 rounded-xl shadow border border-slate-200">
    <h1 class="text-xl font-bold text-slate-800 mb-2">Recover Password</h1>
    <p class="text-sm text-slate-500 mb-5">Enter your registered email address below to receive password restoration metrics.</p>

    <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700">Email Workspace Address</label>
            <input type="email" name="email" required class="w-full mt-1 p-2 border rounded-md">
        </div>
        <button type="submit" class="w-full bg-teal-600 text-white p-2 rounded-md hover:bg-teal-700 transition">Request Recovery Connection</button>
    </form>
</div>


</body>
</html>