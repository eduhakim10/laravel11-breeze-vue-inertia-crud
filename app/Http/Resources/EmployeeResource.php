<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'company' => new CompanyResource($this->whenLoaded('company')), // Assuming 'company' is a relationship
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }
}
