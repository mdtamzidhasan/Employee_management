<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateApiKey extends Command
{
    protected $signature = 'apikey:generate {name : Service name, e.g. "Reporting Service"}';
    protected $description = 'Generate a new API key for service-to-service communication';

    public function handle(): void
    {
        $name = $this->argument('name');

        $plainKey  = 'sk_' . Str::random(48);
        $hashedKey = hash('sha256', $plainKey);

        ApiKey::create([
            'name'      => $name,
            'key'       => $hashedKey,
            'is_active' => true,
        ]);

        $this->info('API Key generated successfully!');
        $this->warn('Copy this key now — it will not be shown again:');
        $this->line('');
        $this->line($plainKey);
        $this->line('');
    }
}