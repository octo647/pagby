<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CleanExpiredSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:clean {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean expired session files to prevent session errors';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sessionPath = storage_path('framework/sessions');
        
        if (!File::exists($sessionPath)) {
            $this->error('Session directory not found: ' . $sessionPath);
            return 1;
        }

        $sessionLifetime = config('session.lifetime', 120) * 60; // Convert minutes to seconds
        $expiredTime = time() - $sessionLifetime;
        
        $files = File::files($sessionPath);
        $cleanedCount = 0;

        if (!$this->option('force') && !$this->confirm('This will clean expired session files. Continue?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        foreach ($files as $file) {
            $lastModified = $file->getMTime();
            
            // Remove files older than session lifetime
            if ($lastModified < $expiredTime) {
                try {
                    File::delete($file->getPathname());
                    $cleanedCount++;
                } catch (\Exception $e) {
                    $this->error('Failed to delete: ' . $file->getFilename());
                    Log::error('Failed to delete session file', [
                        'file' => $file->getPathname(),
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        $this->info("Cleaned {$cleanedCount} expired session files.");
        Log::info("Session cleanup completed", ['files_cleaned' => $cleanedCount]);
        
        return 0;
    }
}
