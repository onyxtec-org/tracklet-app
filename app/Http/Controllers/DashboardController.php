<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user) {
            if ($user->hasRole('admin')) {
                return redirect()->route('device.requests.list');
            } elseif ($user->hasRole('user')) {
                return redirect()->route('device.request.index');
            }
        }

        $breadcrumbs = [
            ['link'=>"/",'name'=>"Dashboard"], ['name'=>"Dashboard"]
        ];
        return view('dashboard', ['breadcrumbs' => $breadcrumbs]);
    }
}
