<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\JsonResponse;

class GeoController extends Controller
{
    public function getDistricts($state_id): JsonResponse
    {
        $districts = District::where('state_id', $state_id)
            ->where('status', true)
            ->orderBy('name')
            ->select('id', 'name')
            ->get();

        return response()->json($districts);
    }
}
