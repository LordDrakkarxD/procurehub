<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBiddingRequest;
use App\Http\Requests\UpdateBiddingRequest;
use App\Models\Bidding;

class BiddingController extends Controller
{
    public function index()
    {
        return response()->json(
            Bidding::with('company:id,name')->latest()->get()
        );
    }

    public function store(StoreBiddingRequest $request)
    {
        $bidding = Bidding::create($request->validated());

        return response()->json($bidding->load('company:id,name'), 201);
    }

    public function show(Bidding $bidding)
    {
        $bidding->load(['company:id,name', 'contracts:id,bidding_id,number,total_amount,balance,status', 'items.product:id,name,unit']);

        return response()->json($bidding);
    }

    public function update(UpdateBiddingRequest $request, Bidding $bidding)
    {
        $bidding->update($request->validated());

        return response()->json($bidding->load('company:id,name'));
    }

    public function destroy(Bidding $bidding)
    {
        $bidding->delete();

        return response()->json(null, 204);
    }
}