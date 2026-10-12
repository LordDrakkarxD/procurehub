<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Models\BiddingItem;
use App\Models\Contract;

class ContractController extends Controller
{
    public function index()
    {
        return response()->json(
            Contract::with([
                'company:id,name',
                'bidding:id,number,title',
                'items.product:id,name,unit',
            ])->latest()->get()
        );
    }

    public function store(StoreContractRequest $request)
    {
        $data = $request->validated();
        $data['balance'] = $data['total_amount'];

        $contract = Contract::create($data);

        if ($contract->bidding_id) {
            $biddingItems = BiddingItem::where('bidding_id', $contract->bidding_id)->get();

            foreach ($biddingItems as $item) {
                $contract->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total,
                    'quantity_balance' => $item->quantity,
                    'item_number' => $item->item_number,
                ]);
            }
        }

        return response()->json(
            $contract->load(['company:id,name', 'bidding:id,number,title', 'items.product:id,name,unit']),
            201
        );
    }

    public function show(Contract $contract)
    {
        $contract->load(['company:id,name', 'bidding:id,number,title', 'items.product:id,name,unit']);

        return response()->json($contract);
    }

    public function update(UpdateContractRequest $request, Contract $contract)
    {
        $data = $request->validated();

        // Se o valor total mudar e ainda não houver consumo, realinha o saldo
        if (
            isset($data['total_amount'])
            && (float) $contract->balance === (float) $contract->total_amount
        ) {
            $data['balance'] = $data['total_amount'];
        }

        $contract->update($data);

        return response()->json($contract->load(['company:id,name', 'bidding:id,number,title']));
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();

        return response()->json(null, 204);
    }
}