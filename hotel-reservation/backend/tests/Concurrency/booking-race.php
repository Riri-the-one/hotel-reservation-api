<?php

// Real process concurrency test. Never accepts an external database connection.
require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

$root = dirname(__DIR__, 2);
$directory = $root.'/storage/framework/testing';
if (! is_dir($directory)) {
    mkdir($directory, 0777, true);
}
$worker = ($argv[1] ?? '') === '--worker';
$database = $worker ? ($argv[2] ?? '') : $directory.'/race-'.Str::uuid().'.sqlite';
if (dirname($database) !== $directory || ! preg_match('/^race-[a-f0-9-]+\.sqlite$/', basename($database))) {
    throw new RuntimeException('Unsafe concurrency database path.');
}
if (! $worker) {
    $handle = fopen($database, 'x');
    fclose($handle);
}
if (file_exists($root.'/bootstrap/cache/config.php')) {
    throw new RuntimeException('Cached configuration is forbidden for this test.');
}
foreach (['APP_ENV' => 'testing', 'APP_KEY' => 'base64:'.base64_encode(str_repeat('r', 32)), 'DB_URL' => '', 'DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => $database, 'CACHE_STORE' => 'array', 'SESSION_DRIVER' => 'array'] as $key => $value) {
    putenv("$key=$value");
    $_ENV[$key] = $_SERVER[$key] = $value;
}
$app = require $root.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();
if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== $database) {
    throw new RuntimeException('Connection isolation failed.');
}
if ($worker) {
    while (microtime(true) < (float) $argv[3]) {
        usleep(1000);
    }
    try {
        app(BookingService::class)->create(User::firstOrFail(), [
            'room_id' => Room::firstOrFail()->id, 'guest_name' => 'Race Demo',
            'check_in' => now()->addDays(10)->toDateString(), 'check_out' => now()->addDays(12)->toDateString(),
            'guests' => 1, 'request_key' => (string) Str::uuid(),
        ]);
        exit(0);
    } catch (ValidationException $exception) {
        exit(2);
    }
}
try {
    $kernel->call('migrate', ['--force' => true]);
    User::factory()->create();
    $type = RoomType::create(['name' => 'Race Demo', 'capacity' => 2, 'base_price' => 75]);
    Room::create(['room_type_id' => $type->id, 'room_number' => 'R1', 'status' => 'disponible']);
    $start = microtime(true) + 2;
    $processes = [];
    for ($i = 0; $i < 2; $i++) {
        $processes[] = proc_open([PHP_BINARY, __FILE__, '--worker', $database, (string) $start], [0 => ['pipe', 'r'], 1 => ['file', 'php://stdout', 'w'], 2 => ['file', 'php://stderr', 'w']], $pipes, $root);
    }
    $codes = array_map(fn ($process) => proc_close($process), $processes);
    sort($codes);
    if ($codes !== [0, 2] || Reservation::count() !== 1) {
        throw new RuntimeException('Concurrency failed: '.json_encode($codes));
    }
    echo "PASS: two concurrent SQLite processes, one booking, one conflict.\n";
} finally {
    DB::disconnect();
    unlink($database);
}
