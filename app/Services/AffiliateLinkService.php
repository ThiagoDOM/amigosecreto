<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class AffiliateLinkService
{
    /**
     * Convert an arbitrary product URL into an affiliate link if supported.
     */
    public function convertUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $trimmed = trim($url);

        // 1. Amazon: append or update ?tag=
        if ($this->isAmazonUrl($trimmed)) {
            return $this->convertAmazonUrl($trimmed);
        }

        // 2. Shopee: call Shopee Open API to generate an affiliate shortlink (s.shopee.com.br)
        if ($this->isShopeeUrl($trimmed)) {
            return $this->convertShopeeUrl($trimmed);
        }

        // 3. Mercado Livre
        if ($this->isMercadoLivreUrl($trimmed)) {
            return $this->convertMercadoLivreUrl($trimmed);
        }

        return $trimmed;
    }

    /**
     * Process an array of wishlist items and convert any eligible URLs.
     *
     * @param  array<int, array{title: string, notes?: string, url?: string}>  $items
     * @return array<int, array{title: string, notes?: string, url?: string}>
     */
    public function processWishlist(array $items): array
    {
        return array_map(function (array $item) {
            if (! empty($item['url'])) {
                $item['url'] = $this->convertUrl($item['url']);
            }

            return $item;
        }, $items);
    }

    protected function isAmazonUrl(string $url): bool
    {
        return (bool) preg_match('/amazon\.(com\.br|com)|amzn\.to/i', $url);
    }

    protected function isShopeeUrl(string $url): bool
    {
        return (bool) preg_match('/shopee\.(com\.br|com)/i', $url);
    }

    protected function isMercadoLivreUrl(string $url): bool
    {
        return (bool) preg_match('/mercadolivre\.com(\.br)?|produto\.mercadolivre\.com\.br|meli\.la/i', $url);
    }

    protected function convertAmazonUrl(string $url): string
    {
        $tag = config('services.affiliates.amazon.tag');
        if (empty($tag)) {
            return $url;
        }

        $parsed = parse_url($url);
        if (! $parsed || ! isset($parsed['host'])) {
            return $url;
        }

        $query = [];
        if (! empty($parsed['query'])) {
            parse_str($parsed['query'], $query);
        }
        $query['tag'] = $tag;

        $scheme = $parsed['scheme'] ?? 'https';
        $host = $parsed['host'];
        $path = $parsed['path'] ?? '';
        $newQuery = http_build_query($query);

        return "{$scheme}://{$host}{$path}?{$newQuery}";
    }

    /**
     * Convert Shopee URL via Shopee Open API GraphQL.
     */
    protected function convertShopeeUrl(string $url): string
    {
        // If already an affiliate shortlink, leave intact
        if (str_contains($url, 's.shopee.com.br')) {
            return $url;
        }

        $appId = config('services.affiliates.shopee.app_id');
        $secret = config('services.affiliates.shopee.secret');

        if (empty($appId) || empty($secret)) {
            return $url;
        }

        try {
            $timestamp = time();
            $payload = json_encode([
                'query' => 'mutation GenerateShortLink($originUrl: String!) { generateShortLink(input: { originUrl: $originUrl }) { shortLink } }',
                'variables' => [
                    'originUrl' => $url,
                ],
            ]);

            $signature = hash('sha256', $appId.$timestamp.$payload.$secret);

            $response = Http::timeout(3)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => "SHA256 Credential={$appId}, Timestamp={$timestamp}, Signature={$signature}",
                ])
                ->withBody($payload, 'application/json')
                ->post('https://open-api.affiliate.shopee.com.br/graphql');

            if ($response->successful()) {
                $shortLink = $response->json('data.generateShortLink.shortLink');
                if (! empty($shortLink)) {
                    return $shortLink;
                }
            }
        } catch (Throwable $e) {
            Log::debug('Shopee Affiliate API conversion skipped: '.$e->getMessage());
        }

        return $url;
    }

    /**
     * Convert Mercado Livre URL if affiliate tag or conversion is configured.
     */
    protected function convertMercadoLivreUrl(string $url): string
    {
        // If already an affiliate link, leave as-is
        if (str_contains($url, 'meli.la') || str_contains($url, 'mercadolivre.com/sec/')) {
            return $url;
        }

        $tag = config('services.affiliates.mercadolivre.tag');
        if (empty($tag)) {
            return $url;
        }

        $parsed = parse_url($url);
        if (! $parsed || ! isset($parsed['host'])) {
            return $url;
        }

        $query = [];
        if (! empty($parsed['query'])) {
            parse_str($parsed['query'], $query);
        }
        $query['matt_tool'] = $tag;

        $scheme = $parsed['scheme'] ?? 'https';
        $host = $parsed['host'];
        $path = $parsed['path'] ?? '';
        $newQuery = http_build_query($query);

        return "{$scheme}://{$host}{$path}?{$newQuery}";
    }
}
