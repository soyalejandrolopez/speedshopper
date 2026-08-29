@if (session('success') || session('error') || session('info'))
    @php
        $type  = session('success') ? 'success' : (session('error') ? 'error' : 'info');
        $msg   = session('success') ?? session('error') ?? session('info');
    @endphp
    <div id="swal-flash"
         data-type="{{ $type }}"
         data-text="{{ $msg }}"
         data-title=""
         style="display:none" aria-hidden="true"></div>
@endif

@if (session('swal_auth'))
    <div id="swal-auth-flash"
         data-event="swal.{{ session('swal_auth') }}"
         style="display:none" aria-hidden="true"></div>
@endif
