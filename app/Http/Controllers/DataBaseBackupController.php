<?php

namespace App\Http\Controllers;

use App\DataTables\DatabaseBackupDataTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Helpers\Format;
use Spatie\Backup\Tasks\Backup\BackupJob;
use Spatie\Backup\BackupDestination\Backup;
use Illuminate\Support\Facades\File;


class DataBaseBackupController extends Controller
{

    public function index(DatabaseBackupDataTable $dataTable)
    {
        return $dataTable->render('admin.backup.index');
    }

    public function createBackup()
    {
        try {
            // Run the custom Artisan command to create a database backup
            Artisan::call('backup:database');
            // Retrieve the output of the Artisan command
            $output = Artisan::output();
            // Check if the backup file was created successfully
            if (File::exists($filePath = $this->extractBackupFilePath($output))) {
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.backup.created'),
                    'alert-type' => trans('quickadmin.alert-type.success'),
                    'file_path' => $filePath,
                ]);
            } else {
                // Handle the case where the file was not created
                return response()->json([
                    'success' => false,
                    'message' => trans('messages.backup.failed'),
                    'alert-type' => trans('quickadmin.alert-type.error'),
                    'error' => 'Backup file not created.',
                ], 500);
            }
        } catch (\Exception $e) {
            // Handle any exceptions that may occur during the backup process
            dd($e->getMessage());
           // \Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => trans('messages.backup.failed'),
                'alert-type' => trans('quickadmin.alert-type.error'),
                'error' => $e->getMessage(), // Provide additional error details
            ], 500);
        }
    }

    public function runBackupEmailCommand()
    {
        try {
            // Run the backup:email command
            Artisan::call('backup:email');

            // Get the output of the command
            $output = Artisan::output();

            return response()->json(['message' => 'Command executed successfully', 'output' => $output]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function extractBackupFilePath($output)
    {
        // Parse the output to find the backup file path
        preg_match('/Backup file created at: (.+)/', $output, $matches);

        return isset($matches[1]) ? trim($matches[1]) : null;
    }

    public function restoreBackup(Request $request)
    {
        try {
            $fileName = $request->fileName;
            $backupPath = storage_path('app/db_backups/');
            $filePath = $backupPath . $fileName;
            // Drop all tables in the current database
            DB::statement('SET foreign_key_checks = 0');
            $tables = config('db_backup_tables.delete_tables');
            foreach ($tables as $table) {
                DB::statement('DROP TABLE IF EXISTS ' . $table);
            }
            DB::statement('SET foreign_key_checks = 1');
            $pdo = DB::connection()->getPdo();
            $pdo->exec(file_get_contents($filePath));
            if ($pdo->errorCode() !== '00000') {
                throw new \Exception('Error importing database: ' . implode(', ', $pdo->errorInfo()));
            }

            return response()->json([
                'success' => true,
                'message' => trans('messages.backup.restored'),
                'alert-type' => trans('quickadmin.alert-type.success'),
            ]);

        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error restoring database.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function deleteBackup(Request $request)
    {
        $fileName = $request->fileName;
        $backupPath = storage_path('app/db_backups/');
        $filePath = $backupPath . $fileName;
        try {
            if (File::exists($filePath)) {
                File::delete($filePath);
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.backup.deleted'),
                    'alert-type' => trans('quickadmin.alert-type.success'),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => trans('messages.backup.not_found'),
                    'alert-type' => trans('quickadmin.alert-type.error'),
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('messages.backup.delete_failed'),
                'alert-type' => trans('quickadmin.alert-type.error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function downloadBackup($fileName)
    {
        $backupPath = storage_path('app/db_backups/');
        $filePath = $backupPath . $fileName;
        // Validate file existence and accessibility
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return response()->json([
                'success' => false,
                'title' => 'File Not Found',
                'message' => 'The backup file could not be found or accessed.',
                'alert-type' => trans('quickadmin.alert-type.success'),
            ], 404);
        }

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/sql' // Set appropriate content type
        ]);
    }


    public function uploadBackup(Request $request)
    {
       // dd($request->all());
        $request->validate([
            'backup_file' => 'required|file',
        ]);
        $file = $request->file('backup_file');
        if ($file->getClientOriginalExtension() !== 'sql') {
            return response()->json([
                'success' => false,
                'message' => trans('messages.backup.failed'),
                'alert-type' => trans('quickadmin.alert-type.error'),
                'error' => 'Invalid file type. Please upload an SQL file.', // Provide additional error details
            ], 500);
        }

        $file = $request->file('backup_file');
        $path = $file->storeAs('', $file->getClientOriginalName(), 'backups');
        return response()->json([
            'success' => true,
            'title' => 'Upload Successful',
            'message' => 'The backup file has been uploaded successfully.',
            'alert-type' => trans('quickadmin.alert-type.success'),
            'file_path' => $path, // You can include the file path in the response if needed
        ],200);

    }
}
