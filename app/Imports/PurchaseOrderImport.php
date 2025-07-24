<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Exception;

class PurchaseOrderImport implements ToCollection
{
    /**
     * This public property will hold the structured data after parsing is complete.
     * @var array
     */
    public array $data;

    /**
     * This is the main method called by the Excel package.
     *
     * @param Collection $rows
     * @throws Exception
     */
    public function collection(Collection $rows)
    {
        $headerData = [
            'supplier_name' => null,
            'order_number' => 'PO-' . date('Ymd-His'),
            'order_date' => now()->format('Y-m-d'),
            'final_total_usd' => 0.0, // To store the true total from the "TOTAL" line
        ];
        $items = [];
        $isItemSection = false;
        $columnMap = [];

        foreach ($rows as $row) {
            $rowString = implode(',', $row->toArray());

            // --- 1. Parse Header & Footer Information ---
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
            if (str_contains($rowString, 'TOTAL') && str_contains($rowString, 'USD')) {
                foreach ($row as $cell) {
                    $cleanedCell = str_replace(['"', ','], '', $cell ?? '');
                    if (is_numeric($cleanedCell)) {
                        // Continuously overwrite the total. The last number found on the line will be the final value.
                        $headerData['final_total_usd'] = (float) $cleanedCell;
                    }
                }
            }

            // --- 2. Control the Item Parsing Section ---
            if (str_contains($rowString, 'Delivery Charge')) {
                $isItemSection = false;
                continue;
            }
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
                continue;
            }

            // --- 3. Process Item Rows using the Dynamic Map ---
            if ($isItemSection && !empty($columnMap)) {
                $idx = $row->get($columnMap['idx'] ?? -1);
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

        // --- 4. Final Validation ---
        if (empty($items)) {
            throw new Exception("The importer could not find any valid item rows after the header. Please check the file format.");
        }
        if ($headerData['final_total_usd'] <= 0) {
             throw new Exception("The importer could not find the final 'TOTAL' amount in USD. Please check the file format.");
        }

        // --- 5. Assign the parsed data to the public property ---
        $this->data = [
            'header' => $headerData,
            'items' => $items,
        ];
    }
}
