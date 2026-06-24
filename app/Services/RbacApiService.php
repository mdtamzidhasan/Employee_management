<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RbacApiService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.rbac.api_url');
    }

    // ── User এর সব Permission আনো (Cache সহ) ─────────────────
    public function getUserPermissions(int $userId): array
    {
        $cacheKey = "user_permissions_{$userId}";

        return Cache::remember($cacheKey, 300, function () use ($userId) {
            try {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                ])->timeout(5)->get("{$this->baseUrl}/check/{$userId}");

                if ($response->failed()) {
                    return [];
                }

                return $response->json('permissions', []);

            } catch (\Exception $e) {
                Log::error('RBAC API: Failed to fetch permissions', [
                    'user_id' => $userId,
                    'error'   => $e->getMessage(),
                ]);
                return [];
            }
        });
    }

    // ── User এর Role আনো ─────────────────────────────────────
    public function getUserRole(int $userId): ?array
    {
        $cacheKey = "user_role_{$userId}";

        return Cache::remember($cacheKey, 300, function () use ($userId) {
            try {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                ])->timeout(5)->get("{$this->baseUrl}/check/{$userId}");

                if ($response->failed()) {
                    return null;
                }

                return $response->json('role');

            } catch (\Exception $e) {
                return null;
            }
        });
    }

    // ── নির্দিষ্ট Permission আছে কিনা Check করো ──────────────
    public function hasPermission(int $userId, string $permission): bool
    {
        $permissions = $this->getUserPermissions($userId);
        return in_array($permission, $permissions);
    }

    // ── Permission Cache Clear করো (Role change হলে) ─────────
    public function clearUserCache(int $userId): void
    {
        Cache::forget("user_permissions_{$userId}");
        Cache::forget("user_role_{$userId}");
    }
}