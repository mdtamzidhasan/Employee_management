<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'role'  => $this->role,
            'employee' => [
                'phone'         => $this->employee?->phone,
                'department'    => $this->employee?->department,
                'position'      => $this->employee?->position,
                'salary'        => $this->employee?->salary,
                'joining_date'  => $this->employee?->joining_date?->format('Y-m-d'),
                'address'       => $this->employee?->address,
                'status'        => $this->employee?->status,
                'profile_photo' => $this->employee?->profile_photo,
            ],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}