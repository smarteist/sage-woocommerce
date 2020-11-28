<?php

namespace Hexbit\Woocommerce\Tests;

putenv('TESTS_PATH=' . __DIR__);
putenv('LIBRARY_PATH=' . dirname(__DIR__, 1));
$vendor = dirname(__DIR__, 1) . '/vendor';

if (!is_dir($vendor)) {
    die('Please install via Composer before running tests.');
}

require_once $vendor . '/autoload.php';

// bootstrap jobs todo