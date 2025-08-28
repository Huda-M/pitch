<?php

namespace App\Http\Controllers;

use App\Models\Investors;
use App\Http\Requests\StoreInvestorsRequest;
use App\Http\Requests\UpdateInvestorsRequest;

class InvestorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreInvestorsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Investors $investors)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Investors $investors)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvestorsRequest $request, Investors $investors)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Investors $investors)
    {
        //
    }
}
