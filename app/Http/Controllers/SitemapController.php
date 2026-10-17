<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $courses = Course::where('status', true)->get();

        $staticUrls = [
            route('home'),
            route('about'),
            route('company'),
            route('chairman'),
            route('mission'),
            route('members'),
            route('facilities'),
            route('courses.index'),
            route('gallery'),
            route('contact'),
            route('enquiry.admission'),
            route('verify.certificate'),
            route('verify.admission'),
            route('payment.index'),
            route('franchise.apply'),
            route('franchise.search'),
            route('franchise.status'),
            route('student.login'),
            route('login'),
            route('privacy'),
            route('terms'),
            route('refund'),
        ];

        $content = view('sitemap', compact('staticUrls', 'courses'))->render();

        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }
}
