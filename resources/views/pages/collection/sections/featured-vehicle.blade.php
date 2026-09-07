{{-- Every active vehicle, in the admin's order, using one showcase. With a
     single vehicle the output is the page as approved. --}}

<section class="collection-section collection-featured" id="featured-vehicle">
    <div class="lux-container">
        @foreach ($vehicles as $vehicle)
            @include('pages.collection.partials.vehicle-showcase', [
                's' => $s,
                'vehicle' => $vehicle,
                'showEyebrow' => $loop->first,
            ])
        @endforeach
    </div>
</section>
