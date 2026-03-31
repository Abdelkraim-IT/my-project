<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Illness extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'name',
        'danger',
        
        
    ];






    public function medicine():object{
        return $this->BelongsToMany(Medicine::class); 

    }

    public function patient():object{
        return $this->BelongsToMany(patient::class); 

    }
    public function PatientIllness():object{
        return $this->hasMany(PatientIllness::class); 

    }
}

