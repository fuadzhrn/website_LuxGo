{{-- Rendered by the admin layout, so any controller can report an outcome with
     a plain session flash and nothing else. --}}

@if (session('success'))
    <x-admin.ui.alert type="success" :message="session('success')" />
@endif

@if (session('error'))
    <x-admin.ui.alert type="error" :message="session('error')" />
@endif

@if (session('warning'))
    <x-admin.ui.alert type="warning" :message="session('warning')" />
@endif

@if ($errors->any() && ! session('error'))
    <x-admin.ui.alert type="error" message="Unable to save changes. Please review the highlighted fields." />
@endif
