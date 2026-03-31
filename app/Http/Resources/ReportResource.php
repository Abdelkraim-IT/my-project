<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
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
            'from_date'=>$this->from_date,
            'to_date'=>$this->to_date,
            'type'=>$this->type,
            'number_of_patients'=>$this->number_of_patients,
            'number_of_bookings'=>$this->number_of_bookings
    ];
    }
}
