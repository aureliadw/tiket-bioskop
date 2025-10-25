<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - HappyCine</title>

  {{-- Tailwind CDN --}}
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased">

  <div class="min-h-screen flex items-center justify-center px-4 py-12" 
       style="background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 100%);">

    <div class="max-w-md w-full">

      {{-- Logo/Header --}}
      <div class="text-center mb-8">
        <h1 class="text-4xl font-extrabold text-white mb-2 flex items-center justify-center gap-1">
          <span class="text-red-600">H</span>
          <svg xmlns="http://www.w3.org/2000/svg" 
               class="w-7 h-7 text-white-600 inline-block" 
               fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2l2.39 7.26h7.63l-6.18 4.49 2.36 7.25L12 16.77l-6.2 4.23 2.36-7.25L2 9.26h7.61z"/>
          </svg>
          <span class="text-red-600">PPY</span>
          <span class="text-white-600">CINE</span>
        </h1>
        <p class="text-gray-400">Login to your account</p>
      </div>

      {{-- Login Form --}}
      <div class="rounded-xl p-8 shadow-2xl border" 
           style="background: rgba(20, 20, 20, 0.8); backdrop-filter: blur(10px); border-color: rgba(75, 85, 99, 0.3);">
        <form method="POST" action="{{ route('login') }}">
          @csrf

          {{-- Email --}}
          <div class="mb-6">
            <label for="email" class="block text-sm font-semibold text-gray-300 mb-2">Email</label>
            <input 
              type="email" 
              id="email" 
              name="email" 
              value="{{ old('email') }}"
              required 
              autofocus
              class="w-full px-4 py-3 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-600 transition-all"
              style="background: rgba(31, 41, 55, 0.6); border: 1px solid rgba(75, 85, 99, 0.5);"
              placeholder="Enter your email">
            @error('email')
              <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- Password --}}
          <div class="mb-6">
            <label for="password" class="block text-sm font-semibold text-gray-300 mb-2">Password</label>
            <input 
              type="password" 
              id="password" 
              name="password" 
              required
              class="w-full px-4 py-3 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-600 transition-all"
              style="background: rgba(31, 41, 55, 0.6); border: 1px solid rgba(75, 85, 99, 0.5);"
              placeholder="Enter your password">
            @error('password')
              <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- Remember Me --}}
          <div class="flex items-center justify-between mb-6">
            <label class="flex items-center">
              <input 
                type="checkbox" 
                name="remember" 
                class="w-4 h-4 rounded text-red-600 focus:ring-red-600"
                style="background: rgba(31, 41, 55, 0.6); border-color: rgba(75, 85, 99, 0.5);">
              <span class="ml-2 text-sm text-gray-400">Remember me</span>
            </label>
          </div>

          {{-- Submit Button --}}
          <button 
            type="submit"
            class="w-full py-3 px-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors duration-200 shadow-lg hover:shadow-red-600/50">
            Login
          </button>

          {{-- Register Link --}}
          <div class="mt-6 text-center">
            <p class="text-gray-400 text-sm">
              Don't have an account? 
              <a href="{{ route('register') }}" 
                 class="text-red-500 hover:text-red-400 font-semibold transition-colors">
                Register here
              </a>
            </p>
          </div>
        </form>
      </div>

    </div>
  </div>

</body>
</html>
