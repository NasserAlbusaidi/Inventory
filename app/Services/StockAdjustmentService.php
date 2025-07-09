<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Location;
use App\Models\LocationInventory;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockAdjustmentService
{
    public function adjustStock(array $data): void
    {
        $adjustable = $data['has_variants']
            ? ProductVariant::find($data['selected_variant_id'])
            : Product::find($data['product_id']);

        if (!$adjustable) {
            throw new \Exception('Could not find the item to adjust stock for.');
        }

        DB::transaction(function () use ($adjustable, $data) {
            $inventory = LocationInventory::firstOrNew([
                'location_id' => $data['location_id'],
                'inventoriable_id' => $adjustable->id,
                'inventoriable_type' => $adjustable->getMorphClass(),
            ]);

            $currentStock = $inventory->stock_quantity ?? 0;
            $adjustmentAmount = (int) $data['quantity'];

            switch ($data['adjustment_type']) {
                case 'addition':
                    $inventory->stock_quantity += $adjustmentAmount;
                    break;

                case 'deduction':
                    if ($currentStock < $adjustmentAmount) {
                        throw ValidationException::withMessages([
                            'quantity' => 'Deduction amount (' . $adjustmentAmount . ') cannot be greater than the current stock (' . $currentStock . ').'
                        ]);
                    }
                    $inventory->stock_quantity -= $adjustmentAmount;
                    break;

                case 'set':
                    $inventory->stock_quantity = $adjustmentAmount;
                    break;
            }
            $inventory->save();
        });

        $name = $adjustable->name ?? $adjustable->variant_name;
        Activity::create([
            'type' => 'stock_adjustment',
            'description' => sprintf(
                '%s stock for %s at %s by %d. Notes: %s',
                ucfirst($data['adjustment_type']),
                $name,
                Location::find($data['location_id'])->name,
                abs($data['quantity']),
                $data['notes']
            ),
        ]);
    }
}
