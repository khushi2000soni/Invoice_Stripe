@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('title')@lang('quickadmin.backup.title') @endsection
@section('customCss')
<meta name="csrf-token" content="{{ csrf_token() }}" >
<link rel="stylesheet" href="{{ asset('admintheme/assets/css/printView-datatable.css')}}">

<style>
    .custom-select2 select{
        width: 200px;
        z-index: 1;
        position: relative;
    }
    .custom-select2 .form-control-inner{
        position: relative;
    }
    .custom-select2 .form-control-inner label{
        position: absolute;
        left: 10px;
        top: -8px;
        background-color: #fff;
        padding: 0 5px;
        z-index: 1;
        font-size: 12px;
    }
    .select2-results{
        padding-top: 48px;
        position: relative;
    }
    .select2-link2{
        position: absolute;
        top: 6px;
        left: 5px;
        width: 100%;
    }
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 40px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 41px;
    }
    .select2-search--dropdown .select2-search__field{
        padding: 10px;
        font-size: 15px;
    }
    .select2-search--dropdown .select2-search__field:focus{
        outline: none;
    }
    .select2-link2 .btns {
        color: #3584a5;
        background-color: transparent;
        border: none;
        font-size: 14px;
        padding: 7px 15px;
        cursor: pointer;
        border: 1px solid #3584a5;
        border-radius: 60px;
    }
    #centerModal, #editCategoryModal{
        z-index: 99999;
    }
    #centerModal::before, #editCategoryModal::before{
        display: none;
    }
    .modal-open .modal-backdrop.show{
        display: block !important;
        z-index: 9999;
    }
    .cart_filter_box{
        border-bottom: 1px solid #e5e9f2;
        padding-bottom: 4px;
    }
    .select2-dropdown{
        z-index: 99999;
    }
</style>

@endsection

@section('main-content')

<section class="section roles" style="z-index: unset">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card pt-2">
                    <div class="card-body">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>@lang('quickadmin.backup.backup-management')</h4>
                            {{-- @can('backup_create')
                            <button type="button" class="addnew-btn takebackup circlebtn" data-href="{{ route('backups.create')}}"><x-svg-icon icon="create-backup" /></button>
                            @endcan --}}

                            @can('backup_upload')
                            <form id="UploadNewBackupForm" method="post" enctype="multipart/form-data" action="{{route('backups.upload')}}">
                                <input type="file" id="backup_file" name="backup_file" hidden>
                                <label for="backup_file" class="row author-box align-items-center gap-4 " id="upload_error">
                                    <div class="addnew-btn uploadfile circlebtn"><x-svg-icon icon="upload" /></div>
                                </label>
                            </form>
                            @endcan
                          </div>
                        <div class="table-responsive fixed_Search">
                            {{$dataTable->table(['class' => 'table dt-responsive', 'style' => 'width:100%;','id'=>'dataaTable'])}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
          {{-- @dd(session()->all()) --}}
        </div>
  </section>

@endsection

@section('customJS')
{!! $dataTable->scripts() !!}
  <script src="{{ asset('admintheme/assets/bundles/datatables/datatables.min.js') }}"></script>
  <script src="{{ asset('admintheme/assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('admintheme/assets/bundles/jquery-ui/jquery-ui.min.js') }}"></script>
  <!-- Page Specific JS File -->
  <script src="{{ asset('admintheme/assets/js/page/datatables.js') }}"></script>

<script>
   $(document).ready(function () {

        var DataaTable = $('#dataaTable').DataTable();

        // Page show from top when page changes
        $(document).on('draw.dt','#dataaTable', function (e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: 0
            }, 'fast');
        });

        $(document).on('click', '.takebackup', function (e) {
            e.preventDefault();
            var formAction = $(this).data('href');

            $.ajax({
                url: formAction,
                type: 'POST',
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                        var alertType = response['alert-type'];
                        var message = response['message'];
                        var title = "{{ trans('quickadmin.backup.title') }}";
                        showToaster(title,alertType,message);
                    // location.reload();
                    DataaTable.ajax.reload();

                },
                error: function (xhr) {
                    var errorMessage = "An error occurred while processing your request.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    swal("{{ trans('quickadmin.backup.title') }}", errorMessage, 'error');
                }
            });
        });


        $(document).on('click', '.backup_delete_btn', function(e){
            e.preventDefault();

            var formaction = $(this).data('action');
            var fileName = $(this).data('file-name');
            swal({
            title: "{{ trans('messages.deletetitle') }}",
            text: "{{ trans('messages.areYouSure') }}",
            icon: 'warning',
            buttons: {
            confirm: 'Yes, delete it',
            cancel: 'No, cancel',
            },
            dangerMode: true,
            }).then((willDelete) => {
            if (willDelete) {
                // If the user confirms, send the DELETE request
                $.ajax({
                url: formaction,
                type: 'POST',
                data: { fileName: fileName },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var alertType = response['alert-type'];
                        var message = response['message'];
                        var title = "{{ trans('quickadmin.backup.title') }}";
                        showToaster(title,alertType,message);
                        DataaTable.ajax.reload();
                        // location.reload();

                },
                error: function (xhr) {
                    // Handle error response
                    var errorMessage = "An error occurred while deleting the backup.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    swal("{{ trans('quickadmin.backup.title') }}", errorMessage, 'error');
                }
                });
            }
            });
        });

        $(document).on('click', '.backup-restore-btn', function(e){
            e.preventDefault();
            var formaction = $(this).data('action');
            var fileName = $(this).data('file-name');
            swal({
            title: "{{ trans('messages.restoretitle') }}",
            text: "{{ trans('messages.areYouSurerestore') }}",
            icon: 'warning',
            buttons: {
            confirm: 'Yes, Restore it',
            cancel: 'No, cancel',
            },
            dangerMode: true,
            }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                url: formaction,
                type: 'POST',
                data: { fileName: fileName },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var alertType = response['alert-type'];
                        var message = response['message'];
                        var title = "{{ trans('quickadmin.backup.title') }}";
                        showToaster(title,alertType,message);
                        DataaTable.ajax.reload();
                },
                error: function (xhr) {
                    // Handle error response
                    var errorMessage = "An error occurred while deleting the backup.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    swal("{{ trans('quickadmin.backup.title') }}", errorMessage, 'error');
                }
                });
            }
            });
        });

        // Upload backup file

        $('#backup_file').change(function(e) {
        e.preventDefault();
        $('#upload_error + div').empty().remove();
        var file = $(this).prop('files')[0];
        $(".error").remove();
        var selectedFile = this.files[0];
        var formData = new FormData($('#UploadNewBackupForm')[0]);
        var formAction = $('#UploadNewBackupForm').attr('action');
        $.ajax({
            url: formAction,
            type: 'POST',
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                    var alertType = response['alert-type'];
                    var message = response['message'];
                    var title = response['title'];
                    showToaster(title,alertType,message);
                    $('#UploadNewBackupForm')[0].reset();
                    DataaTable.ajax.reload();
            },
            error: function (xhr) {
                var errors= xhr.responseJSON.errors;
                var errorHtml = '<p class="error text-danger" style="line-height: 1;">';
                for (const elementId in errors) {
                    for (const error of errors[elementId]) {
                        errorHtml += error + '<br>';
                    }
                }
                errorHtml += '</p>';
                $(errorHtml).insertAfter($("#upload_error"));

                if(xhr.responseJSON.error){
                    swal("{{ trans('quickadmin.backup.title') }}", xhr.responseJSON.error, 'error');
                }
            }
        });
    });

    });
</script>

@endsection
