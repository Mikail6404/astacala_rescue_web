<?php

require __DIR__.'/bootstrap/app.php';
$app = Illuminate\Foundation\Application::getInstance();
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'Sessions table exists: '.(\Illuminate\Support\Facades\Schema::hasTable('sessions') ? 'YES' : 'NO')."\n";

// Also check session configuration
echo 'Session driver: '.config('session.driver')."\n";
echo 'Session lifetime: '.config('session.lifetime')." minutes\n";
echo 'Session table: '.config('session.table', 'sessions')."\n";
echo 'Session connection: '.config('session.connection')."\n";
