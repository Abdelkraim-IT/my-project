<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nurse extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'uuid',
        'name',
        'image',
        'phone',
        'Id_Number',
        'status',
        'password',
        'specialization',
        
    ];
        public function NurseDate():object
    {
        return $this->BelongsTo(NurseDate::class);

    }
}
