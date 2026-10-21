@extends('layouts.centre')

@section('title', 'Resources & Downloads - TECH ERA COMPUTER')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-folder-open text-primary me-2"></i>Resources & Downloads Library
        </h3>
        <p class="text-muted small mb-0">Official syllabi, course prospectuses, branding flex banners, and administrative form templates.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('centre.downloads.certificate') }}" class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-certificate me-1"></i>Official Centre Affiliation Certificate
        </a>
    </div>
</div>

<!-- Category Filter Strip -->
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('centre.downloads.index') }}" class="btn btn-sm {{ !request('category') ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">
                All Resources
            </a>
            @foreach($categories as $cat)
            @if(!empty($cat))
            <a href="{{ route('centre.downloads.index', ['category' => $cat]) }}" class="btn btn-sm {{ request('category') == $cat ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3 text-capitalize">
                {{ str_replace('_', ' ', $cat) }}
            </a>
            @endif
            @endforeach
        </div>
    </div>
</div>

<!-- Downloads Grid -->
<div class="row g-2 g-md-4">
    <!-- Fixed Prominent Resource: Centre Affiliation Certificate -->
    <div class="col-6 col-md-6 col-xl-4">
        <div class="card border-0 rounded-4 shadow-sm h-100 border-start border-4 border-warning">
            <div class="card-body p-2.5 p-sm-3 p-md-4 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2 mb-md-3">
                    <span class="badge bg-warning-subtle text-warning-emphasis fw-bold" style="font-size: 0.65rem;">Official</span>
                    <i class="fa-solid fa-certificate text-warning fs-5 fs-md-3"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1 small fs-md-6">Authorized Certificate</h6>
                <p class="small text-muted mb-2 mb-md-3 d-none d-sm-block" style="font-size: 0.75rem;">Official affiliation certificate issued by Central Directorate with validity and accreditation details.</p>
                <div class="mt-auto">
                    <a href="{{ route('centre.downloads.certificate') }}" class="btn btn-warning text-dark fw-bold rounded-pill w-100 py-1" style="font-size: 0.75rem;" target="_blank">
                        <i class="fa-solid fa-print me-1"></i>Certificate
                    </a>
                </div>
            </div>
        </div>
    </div>

    @forelse($downloads as $item)
    <div class="col-6 col-md-6 col-xl-4">
        <div class="card border-0 rounded-4 shadow-sm h-100">
            <div class="card-body p-2.5 p-sm-3 p-md-4 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2 mb-md-3">
                    <span class="badge bg-primary-subtle text-primary text-capitalize" style="font-size: 0.65rem;">{{ str_replace('_', ' ', $item->category ?? 'Resource') }}</span>
                    <i class="fa-solid fa-file-arrow-down text-primary fs-5 fs-md-3"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1 small fs-md-6 text-truncate">{{ $item->title }}</h6>
                <p class="small text-muted mb-2 mb-md-3 d-none d-sm-block" style="font-size: 0.75rem;">{{ Str::limit($item->description, 70) }}</p>

                <div class="d-flex justify-content-between align-items-center small text-muted mb-2 mb-md-3 d-none d-sm-flex" style="font-size: 0.7rem;">
                    <span><i class="fa-solid fa-database me-1"></i>{{ $item->file_size ?? 'Document' }}</span>
                    <span><i class="fa-solid fa-download me-1"></i>{{ $item->downloads_count }}</span>
                </div>

                <div class="mt-auto">
                    <a href="{{ route('centre.downloads.file', $item->id) }}" class="btn btn-outline-primary rounded-pill w-100 fw-semibold py-1" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-download me-1"></i>Download
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <!-- If no custom downloads seeded, still show default standard board resources -->
    <div class="col-6 col-md-6 col-xl-4">
        <div class="card border-0 rounded-4 shadow-sm h-100">
            <div class="card-body p-2.5 p-sm-3 p-md-4 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2 mb-md-3">
                    <span class="badge bg-info-subtle text-info" style="font-size: 0.65rem;">Syllabus</span>
                    <i class="fa-solid fa-book text-info fs-5 fs-md-3"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1 small fs-md-6">Course Syllabus 2026</h6>
                <p class="small text-muted mb-2 mb-md-3 d-none d-sm-block" style="font-size: 0.75rem;">Official curriculum booklet for ADCA, DCA, PGDCA, Tally Prime certifications.</p>
                <div class="mt-auto">
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-info rounded-pill w-100 fw-semibold py-1" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-eye me-1"></i>Browse Courses
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($downloads->hasPages())
<div class="mt-4">
    {{ $downloads->links() }}
</div>
@endif
@endsection
