<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\State;
use Illuminate\Http\Request;

class CoursePublicController extends Controller
{
    public function index(Request $request)
    {
        $categories = CourseCategory::where('status', true)->get();
        $query = Course::with('category')->where('status', true);

        if ($request->filled('category')) {
            $category = CourseCategory::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('course_name', 'like', "%{$s}%")
                  ->orWhere('course_code', 'like', "%{$s}%")
                  ->orWhere('syllabus', 'like', "%{$s}%");
            });
        }

        $courses = $query->paginate(9)->withQueryString();

        return view('pages.courses.index', compact('courses', 'categories'));
    }

    public function show($slug)
    {
        $course = Course::with(['category', 'subjects'])->where('slug', $slug)->firstOrFail();
        $relatedCourses = Course::where('category_id', $course->category_id)
            ->where('id', '!=', $course->id)
            ->where('status', true)
            ->take(4)
            ->get();
        $states = State::where('status', true)->orderBy('name')->get();

        return view('pages.courses.show', compact('course', 'relatedCourses', 'states'));
    }
}
