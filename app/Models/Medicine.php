<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;
  protected $fillable = [
        'uuid',
        'name',
        'patient_id',
        'price',
        'minage',
        'maxage',
        'order',
        'notes'
        
    ];
    
    public function illness():object{
        return $this->BelongsToMany(Illness::class); 

    }
    public function patient():object{
        return $this->BelongsToMany(Patient::class); 

    }
}
