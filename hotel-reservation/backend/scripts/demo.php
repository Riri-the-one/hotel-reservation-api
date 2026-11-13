<?php

// Portable demo launcher: always creates a new disposable SQLite database.
require dirname(__DIR__).'/vendor/autoload.php';

use Illuminate\Contracts\Console\Kernel;

$root = dirname(__DIR__);
chdir($root);
if (file_exists($root.'/bootstrap/cache/config.php')) {
    throw new RuntimeException('Cached configuration is forbidden for the isolated demo.');
}
$port = (int) ($argv[1] ?? 8000);
if ($port < 1024 || $port > 65535) {
    throw new RuntimeException('Invalid demo port.');
}
$database = $root.'/database/demo-'.bin2hex(random_bytes(16)).'.sqlite';
$handle = fopen($database, 'x');
fclose($handle);
foreach (['APP_ENV' => 'local', 'APP_DEBUG' => 'false', 'APP_KEY' => 'base64:'.base64_encode(random_bytes(32)), 'APP_URL' => "http://127.0.0.1:$port", 'DB_URL' => '', 'DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => $database, 'SESSION_DRIVER' => 'file', 'CACHE_STORE' => 'array', 'QUEUE_CONNECTION' => 'sync'] as $key => $value) {
    putenv("$key=$value");
    $_ENV[$key] = $_SERVER[$key] = $value;
}
$app = require $root.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();
if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== $database) {
    throw new RuntimeException('Demo database isolation failed.');
}
echo "Isolated connection: sqlite / $database\n";
foreach ([['migrate', ['--force' => true]], ['db:seed', ['--class' => 'DemoSeeder', '--force' => true]]] as [$command, $options]) {
    if ($kernel->call($command, $options) !== 0) {
        throw new RuntimeException("Demo command failed: $command");
    }
    echo $kernel->output();
}
exit($kernel->call('serve', ['--host' => '127.0.0.1', '--port' => $port]));
