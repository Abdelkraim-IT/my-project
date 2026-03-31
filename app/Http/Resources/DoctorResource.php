<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
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
            'phone'=>$this->phone,
            'status'=>$this->status,
            'Id_Number'=>$this->Id_Number,
            'image'=>$this->image,
            'password'=>$this->password,
            'specialization'=>$this->specialization

        ];
    }
}
