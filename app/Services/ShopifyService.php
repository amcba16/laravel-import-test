<?php

namespace App\Services;

class ShopifyService
{
    protected $storeDomain;
    protected $accessToken;
    protected $apiVersion;
    protected $collectionId;

    public function __construct()
    {
         $this->storeDomain  = env('SHOPIFY_STORE_DOMAIN');
        $this->accessToken  = env('SHOPIFY_ACCESS_TOKEN');
        $this->apiVersion   = env('SHOPIFY_API_VERSION');
        $this->collectionId = env('SHOPIFY_COLLECTION_ID');
    }

    public function createProduct($product)
    {
        $url = "https://{$this->storeDomain}/admin/api/{$this->apiVersion}/graphql.json";

        // --- CSV FIELD MAPPING --- //
        $title           = $product->title;
        $handle          = $product->handle;
        $vendor          = $product->vendor;
        $descriptionHtml = $product->body_html;
        $productType     = $product->product_type;
        $tags            = explode(',', $product->tags);

        // Convert tags array → ["tag1","tag2"]
        $tagsGraphQL = json_encode($tags);

        // ===== GraphQL MUTATION FOR PRODUCT CREATE ===== //
        $query = <<<GQL
mutation {
  productCreate(
    input: {
      title: "{$title}"
      handle: "{$handle}"
      vendor: "{$vendor}"
      descriptionHtml: "{$descriptionHtml}"
      productType: "{$productType}"
      tags: {$tagsGraphQL}
    }
  ) {
    product {
      id
      title
    }
    userErrors {
      field
      message
    }
  }
}
GQL;

        $payload = json_encode(['query' => $query]);

        // ===== CURL REQUEST ===== //
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "X-Shopify-Access-Token: {$this->accessToken}",
            ],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CAINFO => base_path('certs/cacert.pem'),
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);

        curl_close($curl);

        if ($error) {
            return "CURL Error: $error";
        }

        $decoded = json_decode($response, true);

        // ===== Extract Product ID ===== //
        $productId = $decoded['data']['productCreate']['product']['id'] ?? null;

        if (!$productId) {
            return "Product creation failed!";
        }

        // ================================
        //  2️⃣ ADD PRODUCT TO COLLECTION
        // ================================
        return $this->addProductToCollection($productId);
    }


    /**
     * Add created product into collection using provided cURL code
     */
    private function addProductToCollection($productId)
    {
        $url = "https://{$this->storeDomain}/admin/api/{$this->apiVersion}/graphql.json";

        $collectionId = $this->collectionId; // from ENV

        // Your reference mutation
        $query = <<<GQL
mutation collectionAddProducts(\$id: ID!, \$productIds: [ID!]!) {
  collectionAddProducts(id: \$id, productIds: \$productIds) {
    collection {
      id
      title
      products(first: 10) {
        nodes {
          id
          title
        }
      }
    }
    userErrors {
      field
      message
    }
  }
}
GQL;

        $variables = [
            "id"         => $collectionId,
            "productIds" => [$productId]
        ];

        $payload = json_encode([
            "query"     => $query,
            "variables" => $variables
        ]);

        // CURL CALL
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "X-Shopify-Access-Token: {$this->accessToken}",
            ],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CAINFO => base_path('certs/cacert.pem'),
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);

        curl_close($curl);

        if ($error) {
            return "CURL Error: $error";
        }
        return json_decode($response, true);
    }
}
