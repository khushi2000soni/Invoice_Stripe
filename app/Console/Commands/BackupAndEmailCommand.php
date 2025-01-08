<?php

namespace App\Console\Commands;

use App\Mail\ShareBackupFileMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BackupAndEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:email';
    protected $description = 'Backup, email, and truncate on December 31st.';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Executing backup, email, and truncate tasks...');
        // Run the createbackup command
        Artisan::call('backup:database');
        $backupFilePath = Cache::get('database_backup_filepath');
         // Send email with the backup file
        //dd($backupFilePath);
         $this->sendBackupEmail($backupFilePath);
        // Truncate orders and order_product tables
        $this->truncateTables();
        $this->info('Tasks completed successfully.');
    }

    protected function sendBackupEmail($backupFilePath)
    {
        $to = getSetting('company_email') ?? '';
        $subject = 'Backup File ' . now()->format('d_M_Y_H_i_s');
        // Use the ShareBackupFileMail mail class
        if (!is_null($backupFilePath)) {
            // Use the ShareBackupFileMail mail class
            Mail::to($to)->send(new ShareBackupFileMail($backupFilePath, $subject));
            $this->info('Email sent with the backup file.');
        } else {
            $this->error('Backup file path is null. Email not sent.');
        }
    }

    protected function truncateTables()
    {
        $tables = config('db_backup_tables.delete_tables');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        // Remember to handle foreign key constraints if any
        $this->info('Tables truncated.');
    }
}
