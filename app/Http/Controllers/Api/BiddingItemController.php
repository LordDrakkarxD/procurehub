<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBiddingItemRequest;
use App\Models\Bidding;
use App\Models\BiddingItem;
use Illuminate\Http\Request;

class BiddingItemController extends Controller
{
    public function index(Bidding $bidding)
    {
        return response()->json(
            $bidding->items()->with('product:id,name,unit,price')->get()
        );
    }

    public function store(StoreBiddingItemRequest $request, Bidding $bidding)
    {
        $next = ((int) $bidding->items()->max('item_number')) + 1;

        $item = $bidding->items()->create([
            ...$request->validated(),
            'item_number' => $next,
        ]);

        $this->syncBiddingTotal($bidding);

        return response()->json($item->load('product:id,name,unit,price'), 201);
    }

    public function update(StoreBiddingItemRequest $request, Bidding $bidding, BiddingItem $item)
    {
        abort_unless($item->bidding_id === $bidding->id, 404);

        $item->update($request->validated());
        $this->syncBiddingTotal($bidding);

        return response()->json($item->load('product:id,name,unit,price'));
    }

    public function destroy(Bidding $bidding, BiddingItem $item)
    {
        abort_unless($item->bidding_id === $bidding->id, 404);

        $item->delete();
        $this->syncBiddingTotal($bidding);

        return response()->json(null, 204);
    }

    private function syncBiddingTotal(Bidding $bidding): void
    {
        $bidding->update([
            'total_amount' => $bidding->items()->sum('total'),
        ]);
    }
}
