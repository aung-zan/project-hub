<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateJwtSecret extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:secret';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the secret key for JWT authentication';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            $this->error('.env file not found.');
            return 1;
        }

        $secret = base64_encode(random_bytes(32));

        $envContent = file_get_contents($envPath);

        if (str_contains('JWT_SECRET', $envContent)) {
            // replace existing value
            $envContent = preg_replace('/^JWT_SECRET=.*/m', "JWT_SECRET={$secret}", $envContent);
        } else {
            $envContent .= "\nJWT_SECRET=$secret\n";
        }

        file_put_contents($envPath, $envContent);

        $this->info("JWT Secret generated successfully:");
        $this->info($secret);

        return 0;
    }
}
