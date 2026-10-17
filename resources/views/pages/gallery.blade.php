@extends('layouts.app')

@section('title', 'Photo & Event Gallery | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container text-center text-md-start">
            <h1>Campus & Practical Lab Gallery</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Gallery</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill uppercase mb-2">Moments of Excellence</span>
                <h2 class="fw-extrabold display-6">Life at {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h2>
                <p class="text-muted">Glimpses of computer labs, practical sessions, convocation ceremonies, and skill workshops.</p>
            </div>

            <!-- Categories Badges -->
            @if($categories->isNotEmpty())
            <div class="d-flex justify-content-center flex-wrap gap-2 mb-5">
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 filter-btn active" data-filter="all">All Photos</button>
                @foreach($categories as $cat)
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4 filter-btn" data-filter="{{ Str::slug($cat) }}">{{ $cat }}</button>
                @endforeach
            </div>
            @endif

            <!-- Gallery Grid -->
            <div class="row g-4" id="galleryContainer">
                @foreach($items as $item)
                    <div class="col-lg-4 col-md-6 gallery-item" data-category="{{ Str::slug($item->category) }}">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                            <div class="position-relative" style="height: 240px; background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 100%);">
                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white p-3 text-center">
                                    <i class="fa-solid fa-camera-retro fs-1 mb-2 text-info opacity-75"></i>
                                    <h6 class="fw-bold mb-0 text-white">{{ $item->title }}</h6>
                                    <small class="badge bg-warning text-dark mt-2">{{ $item->category }}</small>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-dark mb-1">{{ $item->title }}</h6>
                                <small class="text-muted"><i class="fa-solid fa-tag text-primary me-1"></i> {{ $item->category }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active', 'btn-primary'));
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.add('btn-outline-secondary'));
                this.classList.add('active', 'btn-primary');
                this.classList.remove('btn-outline-secondary');

                const filter = this.getAttribute('data-filter');
                document.querySelectorAll('.gallery-item').forEach(item => {
                    if (filter === 'all' || item.getAttribute('data-category') === filter) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    </script>
    @endpush
@endsection
