{{-- The .ico carries 16, 32 and 48; the PNGs let a browser skip the container,
     and the 192/512 pair is what a phone uses when the site is added to a home
     screen. Apple's icon is a full opaque square because iOS rounds and
     composites it itself. --}}
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/luxgo/global/logo/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/luxgo/global/logo/favicon-16x16.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/images/luxgo/global/logo/favicon-192x192.png') }}">
<link rel="icon" type="image/png" sizes="512x512" href="{{ asset('assets/images/luxgo/global/logo/favicon-512x512.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/luxgo/global/logo/apple-touch-icon.png') }}">
