<?php

namespace App\Actions\Product;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProcessProductImport
{
    public function execute(array $data): array
    {
        $errors = [];
        $successCount = 0;

        $headers = ['category', 'name', 'description', 'sku', 'has_variants', 'price', 'cost', 'variant_name', 'variant_sku'];

        $productGroups = [];
        $currentProductGroup = null;

        foreach ($data as $rowIndex => $rowArray) {
            if (empty($rowArray) || empty(array_filter($rowArray))) continue;

            $row = array_combine(
                $headers,
                array_slice(array_pad($rowArray, count($headers), null), 0, count($headers))
            );

            if (!empty(trim($row['name'] ?? ''))) {
                $productName = trim($row['name']);
                $currentProductGroup = [
                    'main' => $row,
                    'variants' => [],
                    'rowIndex' => $rowIndex + 2
                ];
                if (strtolower($row['has_variants'] ?? '') === 'yes' && !empty(trim($row['variant_name'] ?? ''))) {
                    $currentProductGroup['variants'][] = $row;
                }
                $productGroups[] = $currentProductGroup;

            } else if ($currentProductGroup && !empty(trim($row['variant_name'] ?? ''))) {
                $lastGroupIndex = count($productGroups) - 1;
                $productGroups[$lastGroupIndex]['variants'][] = $row;
            }
        }

        Log::info("Processed " . count($productGroups) . " product groups for import.");

        DB::transaction(function () use ($productGroups, &$errors, &$successCount) {
            foreach ($productGroups as $group) {
                $mainInfo = $group['main'];
                $variantRows = $group['variants'];
                $productName = trim($mainInfo['name']);

                $validator = Validator::make($mainInfo, [
                    'category' => 'required|string',
                    'name' => 'required|string',
                    // --- FIX 1: Allow the 'has_variants' field to be nullable ---
                    'has_variants' => 'nullable|in:yes,no',
                ]);

                if ($validator->fails()) {
                    $errors["Row {$group['rowIndex']} ({$productName})"] = $validator->errors()->first();
                    continue;
                }

                try {
                    $category = Category::firstOrCreate(['name' => trim($mainInfo['category'])]);

                    // --- FIX 2: Default to false if 'has_variants' is null, empty, or not 'yes' ---
                    // This now safely handles all cases. Laravel will convert the boolean (true/false) to (1/0) for the database.
                    $hasVariants = strtolower(trim($mainInfo['has_variants'] ?? '')) === 'yes';

                    if ($hasVariants && empty($variantRows)) {
                        $errors["Row {$group['rowIndex']} ({$productName})"] = 'Marked as having variants, but no variant rows were provided.';
                        continue;
                    }

                    $product = Product::updateOrCreate(
                        [
                            'name' => $productName,
                            'category_id' => $category->id,
                        ],
                        [
                            'description' => $mainInfo['description'] ?? null,
                            'sku' => $mainInfo['sku'] ?? null,
                            'has_variants' => $hasVariants, // Pass the safe boolean value
                            'selling_price' => !$hasVariants ? ($mainInfo['price'] ?? 0) : null,
                            'cost_price' => !$hasVariants ? ($mainInfo['cost'] ?? 0) : null,
                            'track_inventory' => 1
                        ]
                    );

                    if ($hasVariants) {
                        foreach ($variantRows as $variantRow) {
                            if (empty(trim($variantRow['variant_name'] ?? ''))) continue;

                            ProductVariant::updateOrCreate(
                                [
                                    'product_id' => $product->id,
                                    'variant_name' => trim($variantRow['variant_name']),
                                ],
                                [
                                    'sku' => $variantRow['variant_sku'] ?? null,
                                    'selling_price' => $variantRow['price'] ?? 0,
                                    'cost_price' => $variantRow['cost'] ?? 0,
                                    'track_inventory' => 1,
                                ]
                            );
                        }
                    }
                    $successCount++;
                    Log::info("Successfully imported product '{$productName}' with SKU '{$mainInfo['sku']}'.");
                } catch (\Exception $e) {
                    Log::error("Import Error for product '{$productName}': " . $e->getMessage());
                    $errors["Row {$group['rowIndex']} ({$productName})"] = "A server error occurred: " . $e->getMessage();
                }
            }
        });

        return [$errors, $successCount];
    }
}
