<?php

namespace App\Jobs;

use App\Models\Upload;
use App\Models\Product;
use App\Models\ImportStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use App\Services\ShopifyService;

class ProcessCsvUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $upload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get CSV path
        $path = storage_path('app/public/' . $this->upload->file_path);
        if (!file_exists($path)) return;

        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0); // first row as header

        $records = $csv->getRecords();
        $total = 0;
        $processed = 0;

        $shopify = new ShopifyService();

        foreach ($records as $row) {
            $total++;

            // Create local product
            $product = Product::create([
                'upload_id' => $this->upload->id,
                'handle' => $row['Handle'] ?? null,
                'title' => $row['Title'] ?? 'Untitled',
                'body_html' => $row['Body HTML'] ?? null,
                'vendor' => $row['Vendor'] ?? null,
                'product_type' => $row['Product Type'] ?? null,
                'tags' => $row['Tags'] ?? null,
                'published' => strtolower($row['Published'] ?? 'false') === 'true',
                'variant_sku' => $row['Variant SKU'] ?? null,
                'variant_price' => $row['Variant Price'] ?? 0,
                'variant_compare_at_price' => $row['Variant Compare At Price'] ?? 0,
                'variant_requires_shipping' => strtolower($row['Variant Requires Shipping'] ?? 'true') === 'true',
                'variant_taxable' => strtolower($row['Variant Taxable'] ?? 'true') === 'true',
                'variant_inventory_tracker' => $row['Variant Inventory Tracker'] ?? null,
                'variant_inventory_qty' => $row['Variant Inventory Qty'] ?? 0,
                'variant_inventory_policy' => $row['Variant Inventory Policy'] ?? null,
                'variant_fulfillment_service' => $row['Variant Fulfillment Service'] ?? null,
                'variant_weight' => $row['Variant Weight'] ?? 0,
                'variant_weight_unit' => $row['Variant Weight Unit'] ?? null,
                'image_src' => $row['Image Src'] ?? null,
                'image_position' => $row['Image Position'] ?? null,
                'image_alt_text' => $row['Image Alt Text'] ?? null,
            ]);

            // Send product to Shopify
            $response = $shopify->createProduct($product);
            // Determine status
            $status = 'success';
            $errors = $response['data']['productCreate']['userErrors'] ?? [];
            if (!empty($errors)) {
                $status = 'failed';
            }

            // Save import status
            ImportStatus::create([
                'product_id' => $product->id,
                'status' => $status,
                'error_message' => !empty($errors) ? json_encode($errors) : null,
            ]);

            $processed++;
        }

        // Update totals in upload
        $this->upload->update([
            'total_products' => $total,
            'processed_products' => $processed,
        ]);
    }
}
