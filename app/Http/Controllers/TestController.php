<?php

namespace App\Http\Controllers;

use App\Models\test;
use App\Http\Requests\StoretestRequest;
use App\Http\Requests\UpdatetestRequest;

class TestController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response("aaaa",200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoretestRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(test $test)
    {
        // return new UserResource(User::findOrFail($id));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(test $test)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatetestRequest $request, test $test)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(test $test)
    {
        //
    }
}
