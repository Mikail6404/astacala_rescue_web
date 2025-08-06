<?php

namespace App\Console\Commands;

use App\Services\AstacalaApiClient;
use App\Services\GibranUserService;
use Illuminate\Console\Command;

class TestDataStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:data-structure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test API data structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiClient = app(AstacalaApiClient::class);
        $userService = new GibranUserService($apiClient);

        $this->info('=== TESTING USER DATA STRUCTURE ===');

        // Test user data
        $response = $userService->getAllUsers();

        $this->info('Success: '.($response['success'] ? 'YES' : 'NO'));
        $this->info('Message: '.$response['message']);

        if ($response['success'] && ! empty($response['data'])) {
            $this->info("\nData structure for first user:");
            $firstUser = $response['data'][0];

            $this->info('Data type: '.gettype($firstUser));

            if (is_array($firstUser)) {
                $this->info('Array keys: '.implode(', ', array_keys($firstUser)));
                $this->info('Sample data: '.json_encode($firstUser, JSON_PRETTY_PRINT));
            } elseif (is_object($firstUser)) {
                $this->info('Object properties:');
                $this->line(print_r(get_object_vars($firstUser), true));
            }
        } else {
            $this->error('No data returned or error occurred');
            $this->line('Full response: '.json_encode($response, JSON_PRETTY_PRINT));
        }

        $this->info("\n=== TESTING ADMIN AUTHENTICATION ===");

        // Test admin session data
        $this->info('Admin ID from session: '.(session('admin_id') ?? 'NULL'));
        $this->info('Admin user from session: '.(session('admin_user') ?? 'NULL'));
        $this->info('All session data:');
        $this->line(print_r(session()->all(), true));
    }
}
