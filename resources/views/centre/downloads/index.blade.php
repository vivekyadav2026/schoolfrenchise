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
<div class="row g-4">
    <!-- Fixed Prominent Resource: Centre Affiliation Certificate -->
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 rounded-4 shadow-sm h-100 border-start border-4 border-warning">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge bg-warning-subtle text-warning-emphasis fw-bold">Official Document</span>
                    <i class="fa-solid fa-certificate text-warning fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Authorized Centre Certificate</h5>
                <p class="small text-muted mb-3">Official affiliation certificate issued by Central Directorate with validity and accreditation details.</p>
                <div class="mt-auto">
                    <a href="{{ route('centre.downloads.certificate') }}" class="btn btn-warning text-dark fw-bold rounded-pill w-100" target="_blank">
                        <i class="fa-solid fa-print me-1"></i>View & Print Certificate
                    </a>
                </div>
            </div>
        </div>
    </div>

    @forelse($downloads as $item)
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 rounded-4 shadow-sm h-100">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge bg-primary-subtle text-primary text-capitalize">{{ str_replace('_', ' ', $item->category ?? 'Resource') }}</span>
                    <i class="fa-solid fa-file-arrow-down text-primary fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ $item->title }}</h5>
                <p class="small text-muted mb-3">{{ Str::limit($item->description, 100) }}</p>

                <div class="d-flex justify-content-between align-items-center small text-muted mb-3">
                    <span><i class="fa-solid fa-database me-1"></i>{{ $item->file_size ?? 'Document' }}</span>
                    <span><i class="fa-solid fa-download me-1"></i>{{ $item->downloads_count }} downloads</span>
                </div>

                <div class="mt-auto">
                    <a href="{{ route('centre.downloads.file', $item->id) }}" class="btn btn-outline-primary rounded-pill w-100 fw-semibold">
                        <i class="fa-solid fa-download me-1"></i>Download Material
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <!-- If no custom downloads seeded, still show default standard board resources -->
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 rounded-4 shadow-sm h-100">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge bg-info-subtle text-info">Syllabus</span>
                    <i class="fa-solid fa-book text-info fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Standard All-Course Syllabus 2026-27</h5>
                <p class="small text-muted mb-3">Official curriculum booklet for ADCA, DCA, PGDCA, Tally Prime, and Web Development certifications.</p>
                <div class="mt-auto">
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-info rounded-pill w-100 fw-semibold">
                        <i class="fa-solid fa-eye me-1"></i>Browse Course Directory
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
