<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
  public function toArray(Request $request = null): array
{
    return [
        'uuid' => $this->uuid,
        'patient_id' => $this->patient_id,
        'files' => $this->files,
        'notes' => $this->notes,
        'blood_pressure' => $this->blood_pressure,
        'Body_temperature' => $this->Body_temperature,
        'Heart_rate' => $this->Heart_rate,
        'Breathing_rate' => $this->Breathing_rate,
        'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
    ];
}
}
