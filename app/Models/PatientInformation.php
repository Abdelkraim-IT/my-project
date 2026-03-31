<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientInformation extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'patient_id',
        'files',
        'notes',
        'blood_pressure',
        'Body_temperature',
        'Heart_rate',
        'Breathing_rate'
        
    ];



    public function patient():object{

        return $this->belongsTo(Patient::class);
    }
}
