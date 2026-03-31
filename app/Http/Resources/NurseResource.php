<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NurseResource extends JsonResource
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
            'status'=>$this->status,
            'phone'=>$this->phone,
            'Id_Number'=>$this->Id_Number,
            'image'=>$this->image,
            'password'=>$this->password,
            'specialization'=>$this->specialization

        ];
    }
}
