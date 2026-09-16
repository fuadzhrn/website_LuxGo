{{-- JSON-LD has to sit in the document, so this is the one place a script tag
     is written inline. The payload is built in App\Support\StructuredData. --}}
<script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
