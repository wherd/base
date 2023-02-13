<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    header('Content-type: '.mime_content_type($uri));
    return readfile(__DIR__.'/public'.$uri);
}

require_once __DIR__.'/public/index.php';
