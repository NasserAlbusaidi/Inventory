<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Actions\Product\ProcessProductImport;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use Illuminate\Support\Collection;

class ImportProducts extends Component
{
    use WithFileUploads;
    public Collection $categories;

    public $upload;

    public function rules()
    {
        return [
            'upload' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ];
    }

    public function mount()
    {
        $this->categories = Category::all();
    }

    /**
     * This method is called when the user clicks the "Upload & Import" button.
     */
    public function import()
    {
        $this->validate();

        try {
            // Use a temporary path for the uploaded file
            $path = $this->upload->getRealPath();

            // To handle files without a header row, we set that option to null
            $data = Excel::toArray(new \stdClass(), $path)[0];

            // Assuming the first row in the user's file IS the header, so we can discard it.
            // If the user's file has NO header, comment out the next line.
            $importData = array_slice($data, 1);

            // Call the processing logic
            list($errors, $successCount) = $this->processImport($importData);

            if (!empty($errors)) {
                session()->flash('import_errors', $errors);
            }
            if ($successCount > 0) {
                session()->flash('import_success', "Successfully imported {$successCount} products and their variants.");
            }

        } catch (\Exception $e) {
            Log::error("File Import Failed: " . $e->getMessage());
            session()->flash('import_errors', ['There was an error reading the file. Please ensure it is a valid and correctly formatted CSV or Excel file.']);
        } finally {
            // Clean up the temporary uploaded file
            $this->upload = null;
        }
    }

    /**
     * The core processing logic, now a private helper method.
     * It accepts the data array and returns errors and success count.
     */
    private function processImport(array $data): array
    {
        // Your existing processing logic is ALMOST perfect for this.
        // We just need to map the numeric-indexed rows to associative arrays.
        return (new ProcessProductImport())->execute($data);
    }

    /**
     * This method is called to download the import template.
     */
     public function downloadTemplate()
    {
        // Define the headers for the CSV file
        $headers = ['category', 'name', 'sku', 'description', 'has_variants', 'price', 'cost', 'variant_name', 'variant_sku'];

        // Create the example rows
        $data = [
            $headers, // The first row is the header row
            ['Apparel', 'T-Shirt', 'TEE-001', 'Comfortable cotton shirt', 'yes', '', '', 'Red, S', 'TEE-001-RS'],
            ['', '', '', '', '', '15.00', '7.00', 'Red, M', 'TEE-001-RM'],
            ['Electronics', 'Laptop', 'LP-002', '15-inch Laptop', 'no', '1200.00', '800.00', '', ''],
        ];

        // Create the CSV content in memory
        $file = fopen('php://temp', 'w+');
        foreach ($data as $row) {
            fputcsv($file, $row);
        }
        rewind($file);

        // Get the CSV string
        $csvContent = stream_get_contents($file);
        fclose($file);

        // Return the response as a file download
        return response()->streamDownload(
            fn() => print($csvContent),
            'product_import_template.csv'
        );
    }

    public function render()
    {
        return view('livewire.product.import-products');
    }
}
