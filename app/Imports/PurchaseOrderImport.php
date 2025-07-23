<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Exception;

class PurchaseOrderImport implements ToCollection
{
    public array $data;

    public function collection(Collection $rows)
    {
        $headerData = [
            'supplier_name' => null,
            'order_number' => 'PO-' . date('Ymd-His'),
            'order_date' => now()->format('Y-m-d'),
        ];
        $items = [];
        $isItemSection = false;

        // --- NEW: Dynamic column mapping ---
        // This array will store the actual column index for each header we need.
        $columnMap = [];

        foreach ($rows as $row) {
            $rowString = implode(',', $row->toArray());

            // --- Header Data Parsing (This part works well) ---
            if (str_contains($rowString, 'SILICON2 CO., LTD')) {
                $headerData['supplier_name'] = 'Silicon2';
            }
            if (str_contains($rowString, 'No. :')) {
                preg_match('/No. :\s*(IN[0-9,]+)/', $rowString, $matches);
                if (isset($matches[1])) {
                    $headerData['order_number'] = explode(',', $matches[1])[0];
                }
            }
            if (str_contains($rowString, 'Date :')) {
                preg_match('/Date : ([\d]{4}-[\d]{2}-[\d]{2})/', $rowString, $matches);
                if (isset($matches[1])) {
                    $headerData['order_date'] = $matches[1];
                }
            }

            // --- Stop processing at the end of the item list ---
            if (str_contains($rowString, 'Delivery Charge') || str_contains($rowString, 'TOTAL')) {
                $isItemSection = false;
                break;
            }

            // --- DYNAMIC HEADER DETECTION ---
            // Find the header row and map the column indexes.
            if (str_contains($rowString, 'DESCRIPTION') && str_contains($rowString, 'Q\'TY')) {
                foreach ($row as $index => $header) {
                    $header = strtoupper(trim($header ?? ''));
                    if ($header === 'IDX') $columnMap['idx'] = $index;
                    if ($header === 'DESCRIPTION') $columnMap['description'] = $index;
                    if ($header === 'Q\'TY') $columnMap['quantity'] = $index;
                    if ($header === 'PRICE') $columnMap['price'] = $index;
                    if ($header === 'AMOUNT') $columnMap['amount'] = $index;
                }
                $isItemSection = true;
                continue; // Skip processing the header row itself as an item
            }

            // --- ITEM PROCESSING USING DYNAMIC MAP ---
            if ($isItemSection && !empty($columnMap)) {
                // Use .get() with a default to safely access row data
                $idx = $row->get($columnMap['idx'] ?? -1);

                // Check if it's a valid item row (must have a numeric index)
                if (is_numeric($idx) && (int)$idx > 0) {
                    $items[] = [
                        'description' => trim($row->get($columnMap['description'] ?? -1) ?? ''),
                        'quantity'    => (int)($row->get($columnMap['quantity'] ?? -1) ?? 0),
                        'price'       => (float)($row->get($columnMap['price'] ?? -1) ?? 0),
                        'amount'      => (float)str_replace(',', '', ($row->get($columnMap['amount'] ?? -1) ?? 0)),
                    ];
                }
            }
        }

        if (empty($items)) {
            throw new Exception("The importer could not find any valid item rows after the header. Please check the file format.");
        }

        $this->data = [
            'header' => $headerData,
            'items' => $items,
        ];
    }
}
