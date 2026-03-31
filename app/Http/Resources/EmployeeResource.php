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
            'uuid'=>$this->uuid,
            'name'=>$this->name,
            'ID_number'=>$this->ID_number,
            'status'=>$this->status,
            'image'=>$this->image,
            'password'=>$this->password,
            'phone'=>$this->phone,
            'job'=>$this->job,
            'salary'=>$this->salary
        ];
    }
}
