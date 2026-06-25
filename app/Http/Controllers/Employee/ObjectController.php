<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\RbacApiService;
use Illuminate\Http\Request;

class ObjectController extends Controller
{
    public function __construct(protected RbacApiService $rbac) {}

    // ── Generic Object Page ───────────────────────────────────
    public function show(string $objectSlug)
    {
        $user   = auth()->user()->load('employee');
        $userId = $user->id;

        // Admin সব access পাবে
        if ($user->isAdmin()) {
            $operations = ['view', 'create', 'edit', 'delete', 'export'];
            $objectName = ucfirst(str_replace('_', ' ', $objectSlug));
            $objectMeta = ['object_type' => 'custom', 'department_name' => null];
        } else {
            $rbacData   = $this->rbac->getUserRbacData($userId);
            $operations = [];
            $objectMeta = ['object_type' => 'custom', 'department_name' => null];
            $objectName = ucfirst(str_replace('_', ' ', $objectSlug));

            foreach ($rbacData['objects'] as $obj) {
                if ($obj['slug'] === $objectSlug) {
                    $operations = $obj['operations'];
                    $objectName = $obj['name'];
                    $objectMeta = $obj;
                    break;
                }
            }

            if (empty($operations)) {
                abort(403, 'You do not have access to this resource.');
            }
        }

        // Object type অনুযায়ী data load করো
        $contentData = $this->loadContentData($objectSlug, $objectMeta, $operations, $user);

        return view('employee.object-detail', array_merge(
            compact('objectSlug', 'objectName', 'operations', 'objectMeta'),
            $contentData
        ));
    }

    // ── Object Type অনুযায়ী Data Load করো ───────────────────
    private function loadContentData(
        string $objectSlug,
        array $objectMeta,
        array $operations,
        $user
    ): array {
        $objectType = $objectMeta['object_type'] ?? 'custom';

        return match(true) {

            // নিজের salary
            $objectSlug === 'own_salary' => [
                'viewData' => [
                    'type'   => 'salary',
                    'salary' => $user->employee?->salary,
                    'user'   => $user,
                ],
            ],

            // নিজের document
            $objectSlug === 'own_documents' => [
                'viewData' => [
                    'type' => 'documents',
                    'user' => $user,
                ],
            ],

            // সব employee (HR access)
            $objectSlug === 'employee_profile' => [
                'viewData' => [
                    'type'      => 'employee_list',
                    'employees' => in_array('view', $operations)
                        ? User::with('employee')->where('role', 'employee')->get()
                        : collect(),
                ],
            ],

            // Employee salary manage
            $objectSlug === 'employee_salary' => [
                'viewData' => [
                    'type'      => 'salary_list',
                    'employees' => in_array('view', $operations)
                        ? User::with('employee')->where('role', 'employee')->get()
                        : collect(),
                ],
            ],

            // Department object (dynamic — যেকোনো department)
            $objectType === 'department' => [
                'viewData' => [
                    'type'       => 'department_employees',
                    'department' => $objectMeta['department_name'],
                    'employees'  => in_array('view', $operations)
                        ? User::with('employee')
                               ->where('role', 'employee')
                               ->whereHas('employee', function ($q) use ($objectMeta) {
                                   $q->where('department', $objectMeta['department_name']);
                               })->get()
                        : collect(),
                ],
            ],

            // Reports
            $objectSlug === 'reports' => [
                'viewData' => [
                    'type'         => 'reports',
                    'reports_url'  => config('app.url') . '/reports/',
                ],
            ],

            // Security Logs
            $objectSlug === 'security_logs' => [
                'viewData' => [
                    'type' => 'security_logs',
                    'logs' => in_array('view', $operations)
                        ? \App\Models\SecurityLog::with('user')
                                                  ->latest()
                                                  ->paginate(20)
                        : collect(),
                ],
            ],

            // Custom/Unknown object — generic message
            default => [
                'viewData' => [
                    'type'    => 'generic',
                    'message' => "Content for '{$objectSlug}' will be available soon.",
                ],
            ],
        };
    }
}