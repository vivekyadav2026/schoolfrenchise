<?php

namespace App\Http\Controllers\Centre;

use App\Http\Controllers\Controller;
use App\Models\Centre;
use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CentreDownloadController extends Controller
{
    protected function getAuthCentre(): Centre
    {
        $user = Auth::user();
        $centre = $user->centre;

        if (!$centre && $user->isSuperAdmin()) {
            $centre = Centre::where('status', 'active')->first();
        }

        if (!$centre) {
            abort(403, 'No franchise study centre is linked to your user account.');
        }

        return $centre;
    }

    public function index(Request $request)
    {
        $centre = $this->getAuthCentre();
        $query = Download::where('status', true);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where('title', 'like', "%{$s}%");
        }

        $downloads = $query->latest('id')->paginate(12)->withQueryString();
        $categories = Download::select('category')->distinct()->pluck('category');

        return view('centre.downloads.index', compact('centre', 'downloads', 'categories'));
    }

    public function certificate()
    {
        $centre = $this->getAuthCentre();
        $centre->load(['state', 'district']);

        return view('centre.downloads.certificate', compact('centre'));
    }

    public function downloadFile(Download $download)
    {
        if (!$download->status) {
            abort(404, 'File is currently unavailable.');
        }

        $download->increment('downloads_count');

        if ($download->file_path && Storage::disk('public')->exists($download->file_path)) {
            return Storage::disk('public')->download($download->file_path, $download->title . '.' . pathinfo($download->file_path, PATHINFO_EXTENSION));
        }

        return back()->with('info', "Download for '{$download->title}' registered. Physical document package available at Central Directorate.");
    }
}
