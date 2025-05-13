<?php

namespace Modules\Zoom\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ZoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('zoom::index');
    }
}
