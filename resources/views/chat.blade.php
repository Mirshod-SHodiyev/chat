<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Chat App</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    body {
      background-color: #e5ddd5;
    }
    .whatsapp-green {
      background-color: #075e54;
    }
    .whatsapp-light {
      background-color: #dcf8c6;
    }
    .whatsapp-dark {
      background-color: #25d366;
    }
  </style>
</head>
<body class="h-screen flex flex-col">

  <!-- Navbar -->
  <nav class="whatsapp-green text-white px-6 py-4 flex justify-between items-center">
    <div class="text-2xl font-bold">Chat App</div>
    <div class="flex items-center gap-4">
      <span class="font-semibold">{{ auth()->user()->name }}</span>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-700">
          Logout
        </button>
      </form>
    </div>
  </nav>
  
  <div id="app">
    <!-- Vue.js App component -->
    <App :auth="{{ auth()->user() }}"/>
  </div>

</body>
</html>
