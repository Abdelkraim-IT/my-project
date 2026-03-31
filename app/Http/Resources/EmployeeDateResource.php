<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeDateResource extends JsonResource
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
            'from_hour'=>$this->from_hour,
            'to_hour'=>$this->to_hour,
            'day'=>$this->day,
            'doctor_id'=>$this->doctor_id,
            'number_of_patients'=>$this->number_of_patients,
            'number_of_workDay'=>$this->number_of_workDay,
            'number_of_booking'=>$this->number_of_booking,
            'number_of_operations'=>$this->number_of_operations

        ];
    }
}
