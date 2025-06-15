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

        $headers = ['category', 'name', 'sku', 'description', 'has_variants', 'price', 'cost', 'variant_name', 'variant_sku'];

        // --- NEW, ROBUST GROUPING LOGIC ---
        $productGroups = [];
        $currentProductGroup = null;

        foreach ($data as $rowIndex => $rowArray) {
            if (empty($rowArray) || empty(array_filter($rowArray))) continue;

            $row = array_combine(
                $headers,
                array_slice(array_pad($rowArray, count($headers), null), 0, count($headers))
            );

            // If a product name exists, it's either a simple product or the start of a new variant group.
            if (!empty(trim($row['name'] ?? ''))) {
                $productName = trim($row['name']);

                // Initialize the group for this product
                $currentProductGroup = [
                    'main' => $row,
                    'variants' => [],
                    'rowIndex' => $rowIndex + 2 // Store the starting row number for error reporting
                ];

                // If this row itself is a variant (has_variants is 'yes' and has a variant_name)
                if (strtolower($row['has_variants'] ?? '') === 'yes' && !empty(trim($row['variant_name'] ?? ''))) {
                    $currentProductGroup['variants'][] = $row;
                }

                // Add the newly created group to our list of all groups.
                $productGroups[] = $currentProductGroup;

            }
            // If the product name is empty BUT we have an active group AND a variant name,
            // it's a variant belonging to the last product.
            else if ($currentProductGroup && !empty(trim($row['variant_name'] ?? ''))) {
                // Add this variant row to the *last* group we were working on.
                $lastGroupIndex = count($productGroups) - 1;
                $productGroups[$lastGroupIndex]['variants'][] = $row;
            }
        }

        // --- Now, process each product group ---
        DB::transaction(function () use ($productGroups, &$errors, &$successCount) {
            foreach ($productGroups as $group) {
                $mainInfo = $group['main'];
                $variantRows = $group['variants'];
                $productName = trim($mainInfo['name']);

                $validator = Validator::make($mainInfo, [
                    'category' => 'required|string',
                    'name' => 'required|string',
                    'has_variants' => 'required|in:yes,no',
                ]);

                if ($validator->fails()) {
                    $errors["Row {$group['rowIndex']} ({$productName})"] = $validator->errors()->first();
                    continue;
                }

                try {
                    $category = Category::firstOrCreate(['name' => trim($mainInfo['category'])]);
                    $hasVariants = (strtolower($mainInfo['has_variants']) === 'yes');

                    // If has_variants is yes, but no variant rows were found, it's an error.
                    if ($hasVariants && empty($variantRows)) {
                        $errors["Row {$group['rowIndex']} ({$productName})"] = 'Marked as having variants, but no variant rows were provided.';
                        continue;
                    }

                    $product = Product::updateOrCreate(
                        ['sku' => $mainInfo['sku'] ?? null],
                        [
                            'name' => $productName,
                            'category_id' => $category->id,
                            'has_variants' => $hasVariants,
                            'description' => $mainInfo['description'] ?? null,
                            // Price/Cost are only set on the parent if it does NOT have variants.
                            'selling_price' => !$hasVariants ? ($mainInfo['price'] ?? 0) : null,
                            'cost_price' => !$hasVariants ? ($mainInfo['cost'] ?? 0) : null,
                            'track_inventory' => 1
                        ]
                    );

                    if ($hasVariants) {
                        foreach ($variantRows as $variantRow) {
                            if (empty($variantRow['variant_name'])) continue;
                            ProductVariant::updateOrCreate(

                                [
                                    'product_id' => $product->id,
                                    'variant_name' => trim($variantRow['variant_name']),
                                    'cost_price' => $variantRow['price'] ?? 0,
                                    'selling_price' => $variantRow['cost'] ?? 0,
                                    'track_inventory' => 1,
                                ]
                            );
                        }
                    }
                    $successCount++;
                } catch (\Exception $e) {
                    Log::error("Import Error for product '{$productName}': " . $e->getMessage());
                    $errors["Row {$group['rowIndex']} ({$productName})"] = "A server error occurred.";
                }
            }
        });

        return [$errors, $successCount];
    }
}
