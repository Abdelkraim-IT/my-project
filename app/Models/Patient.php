<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'image',
        'ID_number',
        'weight',
        'length',
        'age',
        'gender',
        'password',
        'phone',
        'address'

    ];


    public function illness():object{
        return $this->BelongsToMany(Illness::class); 

    }
    public function patientInformation():object{

        return $this->hasMany(Patientinformation::class);
    }
    public function PatientIllness():object{
        return $this->hasMany(PatientIllness::class); 

    }
}
