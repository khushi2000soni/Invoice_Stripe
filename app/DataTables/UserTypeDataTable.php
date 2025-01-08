<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UserTypeDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query)
    {
        return datatables()
        ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('name',function($staff){
                return $staff->name ?? "";
            })
            ->editColumn('role',function($staff){
                // $role = $staff->roles->first();
                // return $role ? $role->name : '';
                return $staff->roles->isNotEmpty() ? $staff->roles->first()->name : '';
            })
            ->editColumn('username',function($staff){
                return $staff->username  ?? "";
            })
            ->editColumn('email',function($staff){
                return $staff->email  ?? "";
            })
            ->editColumn('phone',function($staff){
                return $staff->phone  ?? "";
            })
            ->editColumn('created_at', function ($staff) {
                return $staff->created_at->format('d-m-Y h:i A');
            })
            ->addColumn('action',function($staff){
                $action='';
                if (Gate::check('staff_rejoin')) {
                    if (!($staff->hasRole(1))) {
                    $editIcon = view('components.svg-icon', ['icon' => 'staff-rejoin'])->render();
                    $action .= '<button class="btn btn-icon btn-info rejoin-users-btn p-1 mx-1" data-href="'.route('staff.rejoin', $staff->id).'">'.$editIcon.'</button>';
                    }
                }

                return $action;
            })
            ->filterColumn('created_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(users.created_at,'%d-%M-%Y') like ?", ["%$keyword%"]); //date_format when searching using date
            })
            ->filterColumn('role', function ($query, $keyword) {
                $query->whereHas('roles', function ($q) use ($keyword) {
                    $q->where('roles.name', 'like', "%$keyword%");
                });
            })
            ->orderColumn('role', function ($query, $keyword) {
                $query->whereHas('roles', function ($q) use ($keyword) {
                    $q->orderBy('roles.name', 'asc');
                });
            })
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        $query = $model->newQuery()->select(['users.*'])->onlyTrashed();
        if (!(auth()->user()->hasRole(config('app.roleid.super_admin')))) {
            $query->whereNotIn('id', [1]);
        }
        return $this->applyScopes($query);
        // return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {

        return $this->builder()
        ->setTableId('users-table')
        ->parameters([
            'responsive' => true,
            'pageLength' => 70,
            // 'lengthMenu' => [[10, 25, 50, 70, 100, -1], [10, 25, 50, 70, 100, 'All']],
        ])
        ->columns($this->getColumns())
        ->minifiedAjax()
        ->dom('rtip')
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
            Column::make('name')->title(trans('quickadmin.users.fields.name')),
            Column::make('role')->title(trans('quickadmin.users.fields.role')),
            Column::make('username')->title(trans('quickadmin.users.fields.usernameid')),
            Column::make('email')->title(trans('quickadmin.users.fields.email')),
            Column::make('phone')->title(trans('quickadmin.users.fields.phone')),
            Column::make('created_at')->title(trans('quickadmin.users.fields.created_at')),
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
        return 'User_' . date('YmdHis');
    }
}
