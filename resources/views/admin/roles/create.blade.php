
@extends('layouts.app')
@section('title')@lang('quickadmin.roles.fields.add-role.title') @endsection
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
            <form action="" method="POST" id="roleForm" name="roleForm">
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
$('#roleForm').on('submit', function (e) {
    e.preventDefault();

    $("button[type=submit]").prop('disabled',true);
    var formData = $(this).serializeArray();

    $.ajax({
        url: '{{ route('roles.store') }}',
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

                $('#roleForm')[0].reset();
                location.reload();
        },
        error: function (xhr) {
            var errors= xhr.responseJSON.errors;
            console.log(xhr.responseJSON);

            for (const elementId in errors) {
                $("#"+elementId).addClass('is-invalid');
                var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                $(errorHtml).insertAfter($("#"+elementId).parent());
            }
            $("button[type=submit]").prop('disabled',false);
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
        $('#selectedPermissions').val(selectedPermissions);
    });
</script>
@endsection
