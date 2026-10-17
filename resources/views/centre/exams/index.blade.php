@extends('layouts.centre')

@section('title', 'Examination Sessions & Hall Tickets - TECH ERA COMPUTER')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-clipboard-list text-primary me-2"></i>Examinations & Marks Entry
        </h3>
        <p class="text-muted small mb-0">Central Board examination sessions, hall tickets / admit cards, and internal marks evaluation.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('centre.exams.results') }}" class="btn btn-outline-success rounded-pill px-3">
            <i class="fa-solid fa-square-poll-vertical me-1"></i>Published Results
        </a>
    </div>
</div>

<!-- Exams Table -->
<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Exam Code</th>
                        <th>Exam Session Title</th>
                        <th>Target Course</th>
                        <th>Date of Examination</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                    <tr>
                        <td><span class="badge bg-secondary-subtle text-dark font-monospace">{{ $exam->exam_code }}</span></td>
                        <td>
                            <div class="fw-bold text-dark">{{ $exam->exam_title }}</div>
                            <div class="small text-muted">{{ $exam->session_name }}</div>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary">{{ $exam->course?->course_code }}</span>
                            <div class="small text-muted">{{ $exam->course?->course_name }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $exam->exam_date ? $exam->exam_date->format('d M, Y') : 'Announced' }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $exam->status === 'published' ? 'bg-success' : ($exam->status === 'evaluated' ? 'bg-info' : 'bg-warning text-dark') }}">
                                {{ ucfirst($exam->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('centre.exams.hall-tickets', $exam->id) }}" class="btn btn-outline-primary" target="_blank" title="Hall Tickets">
                                    <i class="fa-solid fa-ticket me-1"></i>Hall Tickets
                                </a>
                                <a href="{{ route('centre.exams.marks', $exam->id) }}" class="btn btn-outline-success" title="Enter Marks">
                                    <i class="fa-solid fa-pen-to-square me-1"></i>Marks Entry
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-clipboard-question fs-1 text-black-50 mb-3 d-block"></i>
                            <h5>No Examination Sessions Active</h5>
                            <p class="small text-muted mb-0">Upcoming examination sessions scheduled by the Head Office will appear here.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($exams->hasPages())
        <div class="p-3 border-top">
            {{ $exams->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
