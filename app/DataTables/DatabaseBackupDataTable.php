<?php

namespace App\DataTables;

use App\Models\DatabaseBackup;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class DatabaseBackupDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable($query)
    {
            return datatables()
            ->of($query)
                ->addIndexColumn()
                ->editColumn('file', function ($backup) {
                    return  $backup['file'];
                })
                // ->editColumn('size', function ($backup) {
                //     return $backup['size'];
                // })
                ->editColumn('date', function ($backup) {
                    return $backup['date'];
                })
                ->addColumn('action',function($backup){
                    $action='';
                    if (Gate::check('backup_download')) {
                        $restoreIcon = view('components.svg-icon', ['icon' => 'download'])->render();
                        $action .= '<a  class="btn btn-info backup-downloadbtn" href="'.route('backups.download', ['fileName' => $backup['file']]).'" >'.$restoreIcon.'</a>';
                    }
                    if (Gate::check('backup_restore')) {
                    $restoreIcon = view('components.svg-icon', ['icon' => 'restore-backup'])->render();
                    $action .= '<button  class="btn btn-info backup-restore-btn" data-action="'.route('backups.restore').'" data-file-name="'.$backup['file'].'">'.$restoreIcon.'</button>';
                    }
                    if (Gate::check('backup_delete')) {
                        $deleteIcon = view('components.svg-icon', ['icon' => 'delete'])->render();
                        $action .= '<button type="button" title="'.trans('quickadmin.qa_delete').'" class="btn btn-danger backup_delete_btn btn-sm" data-action="'.route('backups.delete').'" data-file-name="'.$backup['file'].'">'.$deleteIcon.'</button>';
                    }
                    return $action;
                })
                ->rawColumns(['action']);
    }

    public function query(Request $request)
    {
        $backupPath = 'db_backups';
        $backupFiles = Storage::files($backupPath);
        $backups = collect($backupFiles)->map(function ($backupFile) {
            return [
                'file' => pathinfo($backupFile, PATHINFO_BASENAME),
                // 'size' => Storage::size($backupFile),
                'date' => Carbon::createFromTimestamp(Storage::lastModified($backupFile))->format('d-m-Y h:i A'),
            ];
        });
        // Return the collection as-is, without applying scopes
        return $backups;
    }
    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->parameters([
                        'responsive' => true,
                        'pageLength' => 70,
                        'lengthMenu' => [[10, 25, 50, 70, 100, -1], [10, 25, 50, 70, 100, 'All']],
                    ])
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('frtip')
                    ->orderBy(1)
                    ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title(trans('quickadmin.qa_sn'))->orderable(false)->searchable(false),
            Column::make('file')->title(trans('File')),
            //Column::make('size')->title(trans('Size'))->orderable(false)->searchable(false),
            Column::make('date')->title(trans('Date')),
            Column::computed('action')
            ->exportable(false)
            ->printable(false)
            ->width(60)
            ->addClass('text-center')->title(trans('quickadmin.qa_action')),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'DatabaseBackup_' . date('YmdHis');
    }
}
