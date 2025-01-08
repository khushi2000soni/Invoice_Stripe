<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DatabaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';
    protected $description = 'Backup the database';
    protected $backupFilePath;

    /**
     * The console command description.
     *
     * @var string
     */


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $backupPath = storage_path('app/db_backups/');
        // Create the backup directory if it doesn't exist
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $fileName = 'backup_' . now()->format('d_M_Y_H_i_s') . '.sql';
        $filePath = $backupPath . $fileName;
        $this->backupFilePath = $backupPath . $fileName;
        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', '3306');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $database = env('DB_DATABASE');
        try {
            putenv('PATH=' . getenv('PATH') . env('DB_MYSLDUMP_PATH'));
            // for complete database backup
            // $command = 'mysqldump --user=' . $username . ' --password=' . $password . ' --host=' . $host . ' --databases ' . $database . ' > ' . $backupPath . $fileName;
            $deleteTables= config('db_backup_tables.delete_tables');
            // command for taking backup of specific tables only
           // $command = 'mysqldump --user=' . $username . ' --password=' . $password . ' --host=' . $host . ' ' . $database . ' ' . implode(' ', $deleteTables) . ' > ' . $backupPath . $fileName;
           $command = 'mysqldump -u '.$username.' --password='.$password.' -h '.$host.' '.$database.' --no-tablespaces '.implode(' ', $deleteTables).' > '.$backupPath.$fileName;
            $returnVar = null;
            $output = null;
            //dd($command);
            exec($command, $output, $returnVar);
            if ($returnVar !== 0) {
                throw new \Exception('mysqldump command failed: ' . implode(PHP_EOL, $output));
            }
            // Cache the backup file path
            Cache::put('database_backup_filepath', $this->backupFilePath, now()->addMinutes(5));
            $this->info('Database backup completed successfully.');
            $this->info('Backup file created at: ' . $filePath);
        } catch (\Exception $e) {
            //dd($e->getMessage());
            $this->error('Database backup failed. Error: ' . $e->getMessage());
            // Log the exception for debugging
            Log::info($e->getMessage());
            //\Log::error($e->getMessage());
        }
    }
}
