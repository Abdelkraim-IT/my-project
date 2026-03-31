<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
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
            'ID_number'=>$this->ID_number,
            'name'=>$this->name,
            'weight'=>$this->weight,
            'length'=>$this->length,
            'age'=>$this->age,
            'password'=>$this->password,
            'phone'=>$this->phone,
            'address'=>$this->address,
            'image'=>$this->image,
            'gender'=>$this->gender,
       
    ];
    }
}
