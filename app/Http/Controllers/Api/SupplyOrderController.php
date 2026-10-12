<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplyOrderRequest;
use App\Models\Contract;
use App\Models\ContractItem;
use App\Models\SupplyOrder;
use Illuminate\Support\Facades\DB;

class SupplyOrderController extends Controller
{
    public function index()
    {
        return response()->json(
            SupplyOrder::with('contract:id,number,company_id')
                ->latest()
                ->get()
        );
    }

    public function store(StoreSupplyOrderRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $contract = Contract::lockForUpdate()->findOrFail($data['contract_id']);

            $order = SupplyOrder::create([
                'number' => $data['number'],
                'contract_id' => $contract->id,
                'status' => 'pending',
                'ordered_at' => $data['ordered_at'] ?? now()->toDateString(),
                'notes' => $data['notes'] ?? null,
                'amount' => 0,
            ]);

            $total = 0;

            foreach ($data['items'] as $row) {
                $contractItem = ContractItem::where('contract_id', $contract->id)
                    ->lockForUpdate()
                    ->findOrFail($row['contract_item_id']);

                $qty = (float) $row['quantity'];

                if ($qty > (float) $contractItem->quantity_balance) {
                    abort(422, "Quantidade do item {$contractItem->item_number} excede o saldo.");
                }

                $lineTotal = round($qty * (float) $contractItem->unit_price, 2);

                $order->items()->create([
                    'contract_item_id' => $contractItem->id,
                    'quantity' => $qty,
                    'unit_price' => $contractItem->unit_price,
                    'total' => $lineTotal,
                ]);

                $contractItem->decrement('quantity_balance', $qty);
                $total += $lineTotal;
            }

            if ($total > (float) $contract->balance) {
                abort(422, 'O valor da ordem excede o saldo do contrato.');
            }

            $order->update(['amount' => $total]);
            $contract->decrement('balance', $total);

            return response()->json(
                $order->load(['items.contractItem.product:id,name,unit', 'contract:id,number']),
                201
            );
        });
    }

    public function show(SupplyOrder $supplyOrder)
    {
        $supplyOrder->load([
            'contract.company:id,name',
            'items.contractItem.product:id,name,unit',
        ]);

        return response()->json($supplyOrder);
    }

    public function destroy(SupplyOrder $supplyOrder)
    {
        return DB::transaction(function () use ($supplyOrder) {
            $contract = $supplyOrder->contract()->lockForUpdate()->first();

            foreach ($supplyOrder->items as $item) {
                $item->contractItem()->increment('quantity_balance', $item->quantity);
            }

            $contract->increment('balance', $supplyOrder->amount);

            $supplyOrder->delete();

            return response()->json(null, 204);
        });
    }
}
