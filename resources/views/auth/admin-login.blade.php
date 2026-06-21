<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — CastVote</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-orange-700 to-orange-900 flex items-center justify-center">
<div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">CastVote</h1>
        <p class="text-gray-500 mt-1 text-sm">Admin Portal</p>
    </div>

    @if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300 text-orange-600">
            <label for="remember" class="text-sm text-gray-600">Remember me</label>
        </div>
        <button type="submit"
                class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2.5 rounded-lg transition">
            Sign In
        </button>
    </form>
</div>
</body>
</html>
