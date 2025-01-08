<!-- General JS Scripts -->
<script src="{{ asset('admintheme/assets/js/app.min.js') }}"></script>
<!-- Template JS File -->
<script src="{{ asset('admintheme/assets/bundles/prism/prism.js') }}"></script>
<script src="{{ asset('admintheme/assets/js/scripts.js') }}"></script>
<script src="{{ asset('admintheme/assets/bundles/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('admintheme/assets/bundles/izitoast/js/iziToast.min.js') }}"></script>
<script src="{{ asset('admintheme/assets/js/page/toastr.js') }}"></script>
<script src="{{ asset('admintheme/assets/bundles/sweetalert/sweetalert.min.js') }}"></script>
<script src="{{ asset('admintheme/assets/js/page/sweetalert.js') }}"></script>
<script src="{{ asset('admintheme/assets/bundles/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('admintheme/assets/js/jquery.printPage.js') }}"></script>
@include('partials.check-network-script')

<script>

    function showToaster(title,alertType, message) {
        var position = 'topRight'; // You can change the default position here
        var toastSettings = {
            title: title,
            message: message,
            position: position,
        };

        switch (alertType) {
            case 'info':
                iziToast.info(toastSettings);
                break;
            case 'success':
                iziToast.success(toastSettings);
                break;
            case 'warning':
                iziToast.warning(toastSettings);
                break;
            case 'error':
                iziToast.error(toastSettings);
                break;
        }
    }

    $(document).ready(function(){
        // $('select').select2({
        //     selectOnClose: true
        // });
        @if(Session::has('message'))
            var alertType = "{{ Session::get('alert-type') }}";
            var message = "{{ Session::get('message') }}";
            var title = "{{ Session::get('title') }}";
            showToaster(title, alertType, message);
        @endif

	});

    $(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
        $(this).closest(".select2-container").siblings('select:enabled').select2('open');
    });

    $(document).on('click','.paginate_button', function (e) {
        e.preventDefault();
        $(".loader").show();
        setTimeout(function() {
            $(".loader").hide();
        }, 1000);
    });

    function showLoader(){
        $('#pagesubmitloader').css('display','flex');
    }

    function hideLoader(){
        $('#pagesubmitloader').css('display','none');
    }

</script>
