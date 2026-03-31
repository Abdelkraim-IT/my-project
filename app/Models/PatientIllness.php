<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientIllness extends Model
{
    use HasFactory;
 

    protected $fillable = [
        'uuid',
        'patient_id',
        'illness_id',
       
        
    ];


    public function PatientIllness():object{
        return $this->belongsTo(Patient::class); 

    }
    public function PatientIllness1():object{
        return $this->belongsTo(Illness::class); 

    }
}
