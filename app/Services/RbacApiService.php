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

    // ── Full RBAC Data (permissions + objects) আনো ───────────
    public function getUserRbacData(int $userId): array
    {
        // $cacheKey = "rbac_data_{$userId}";

        // return Cache::remember($cacheKey, 300, function () use ($userId) {
            try {
                $response = Http::withHeaders(['Accept' => 'application/json'])
                               ->timeout(5)
                               ->get("{$this->baseUrl}/check/{$userId}");

                if ($response->failed()) {
                    return $this->emptyRbacData();
                }

                return [
                    'permissions' => $response->json('permissions', []),
                    'objects'     => $response->json('objects', []),
                    'role'        => $response->json('role'),
                ];

            } catch (\Exception $e) {
                Log::warning('RBAC API unavailable', ['error' => $e->getMessage()]);
                return $this->emptyRbacData();
            }
       
    }

    // ── শুধু permissions list ─────────────────────────────────
    public function getUserPermissions(int $userId): array
    {
        return $this->getUserRbacData($userId)['permissions'];
    }

    // ── শুধু objects list (sidebar এর জন্য) ──────────────────
    public function getUserObjects(int $userId): array
    {
        return $this->getUserRbacData($userId)['objects'];
    }

    // ── একটা নির্দিষ্ট object এর operations ─────────────────
    public function getObjectOperations(int $userId, string $objectSlug): array
    {
        $objects = $this->getUserObjects($userId);
        foreach ($objects as $object) {
            if ($object['slug'] === $objectSlug) {
                return $object['operations'];
            }
        }
        return [];
    }

    // ── Permission check ──────────────────────────────────────
    public function hasPermission(int $userId, string $permission): bool
    {
        return in_array($permission, $this->getUserPermissions($userId));
    }

    // ── Cache clear ───────────────────────────────────────────
    public function clearUserCache(int $userId): void
    {
        Cache::forget("rbac_data_{$userId}");
    }

    private function emptyRbacData(): array
    {
        return ['permissions' => [], 'objects' => [], 'role' => null];
    }


    public function getGroupedPermissions(int $userId): array
    {
        $permissions = $this->getUserPermissions($userId);

        $grouped = [];

        foreach ($permissions as $permission) {

        // employee.view
        // employee.edit
        // own_salary.export

        $parts = explode('.', $permission);

        if (count($parts) !== 2) {
            continue;
        }

        [$object, $operation] = $parts;

        $grouped[$object][] = $operation;
    }

    return $grouped;
}
}