<x-notification-success message=""></x-notification-success>
<x-notification-error message=""></x-notification-error>
<x-notification-info message=""></x-notification-info>

<script>
    $('document').ready(function() {
    @if(Session::has('errorMessage'))
        GameApi.displayAlertNotification('error', '{{ Session::get('errorMessage') }}');
    @endif
    });
</script>
