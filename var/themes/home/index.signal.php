@extends{'layouts/default'}

@section{'content'}
<header>
  <h1 class="text-2xl font-extrabold font-mono">Welcome to base</h1>
  <h2 class="text-xl">Yet another small PHP framework</h2>
  <nav class="my-4 text-sm text-gray-600">
    <ul>
      <li><a href="https://github.com/wherd/base" target="_blank" rel="nofollow">Github</a></li>
    </ul>
  </nav>
</header>

<section class="space-y-2">
  <h2 class="text-xl mb-2">About this page</h2>
  <p class="text-gray-600">The page you are looking at is being generated dynamically by base.</p>
  <p class="text-gray-600">If you would like to edit this page you will find it located at:</p>
  <pre class="font-mono bg-gray-100 inline text-gray-600 text-sm"><code>var/themes/home/index.signal.php</code></pre>
  <p class="text-gray-600">The corresponding controller for this page can be found at:</p>
  <pre class="font-mono bg-gray-100 inline text-gray-600 text-sm"><code>src/Controllers/HomeController.php</code></pre>
</section>
@end

@section{'footer'}
<footer class="font-mono text-gray-600 text-sm">
  <p>&copy; <?= date('Y') ?> Base is open source project released under the MIT open source licence.</p>
</footer>
@end
