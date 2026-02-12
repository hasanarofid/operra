<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
// use App\Models\SalesOrder; // Assuming it exists or will be linked later

class PublicTrackingController extends Controller
{
    public function index($token = null)
    {
        return Inertia::render('Public/TrackingPortal', [
            'token' => $token,
            // In a real app, logic to fetch order by token would go here
        ]);
    }
}
