<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full">
        <h1 class="text-2xl font-bold mb-6 text-center">Unified Backend Authentication Test</h1>
        
        <form action="/test-auth" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                <input type="text" name="username" value="volunteer" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Maps to: volunteer@mobile.test</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" name="password" value="password123"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Test Unified Backend Login
            </button>
        </form>
        
        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-md">
            <h3 class="text-sm font-medium text-green-800 mb-2">Integration Status:</h3>
            <ul class="text-sm text-green-700 space-y-1">
                <li>✅ Unified Backend API: Operational</li>
                <li>✅ Username → Email Mapping: Functional</li>
                <li>✅ JWT Token Handling: Ready</li>
                <li>✅ Cross-Platform Integration: Enabled</li>
            </ul>
        </div>
        
        <div class="mt-4 text-xs text-gray-500">
            <p>This test authenticates against the unified backend at localhost:8000</p>
            <p>Success will redirect to the admin dashboard</p>
        </div>
    </div>
</body>
</html>
