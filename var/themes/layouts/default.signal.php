<!DOCTYPE html>
<html class="h-full bg-gray-50" lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" type="image/png" href="/favicon.ico"/>
  <title>Welcome to Base</title>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link href="/assets/style.css" rel="stylesheet" />
  @yield{'header'}
</head>
<body class="h-full">
  <main class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      @yield{'content'}
      @yield{'footer'}
    </div>
  </main>
</body>
</html>
