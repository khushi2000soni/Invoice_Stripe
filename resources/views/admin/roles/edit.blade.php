
@extends('layouts.app')
@section('title')@lang('quickadmin.roles.fields.add-role.edit_role') @endsection
@section('customCss')
<meta name="csrf-token" content="{{ csrf_token() }}" >
@endsection

@section('main-content')

<section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12 col-md-12 col-lg-12">
          <div class="card">
            <div class="card-body">
            <form action="" method="POST" id="roleEditForm" name="roleForm">
                @method('PUT')
                @include('admin.roles.form')
            </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>



@endsection

@section('customJS')
<script>
$('#roleEditForm').on('submit', function (e) {
    e.preventDefault();

    $("button[type=submit]").prop('disabled',true);
    var formData = $(this).serializeArray();

    $.ajax({
        url: '{{ route('roles.update',$role->id) }}',
        type: 'POST',
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
        data: formData,
        success: function (response) {
                $('#centerModal').modal('hide');
                var alertType = response['alert-type'];
                var message = response['message'];
                var title = "{{ trans('quickadmin.roles.role') }}";
                showToaster(title,alertType,message);
                 if (response.hasOwnProperty('redirecturl')) {
                    window.location.href = response['redirecturl'];
                 } else {
                     //location.reload();
                 }
        },
        error: function (xhr) {
            var errors= xhr.responseJSON.errors;
            for (const elementId in errors) {
                $("#"+elementId).addClass('is-invalid');
                var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                $(errorHtml).insertAfter($("#"+elementId).parent());
            }
        }
    });
});

</script>

<script>
    var selectedPermissions = [];
    $(document).on('click', '.permission-checkbox', function() {
        var permissionId = $(this).val();
        if ($(this).is(':checked')) {
            // If the checkbox is checked, add the ID to the array
            if (selectedPermissions.indexOf(permissionId) === -1) {
                selectedPermissions.push(permissionId);
            }
        } else {
            // If the checkbox is unchecked, remove the ID from the array
            var index = selectedPermissions.indexOf(permissionId);
            if (index !== -1) {
                selectedPermissions.splice(index, 1);
            }
        }
    });
</script>
@endsection
