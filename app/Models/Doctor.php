<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'name',
        'phone',
        'image',
        'Id_Number',
        'status',
        'password',
        'specialization',
        
    ];
    public function doctorDate():object
    {
        return $this->BelongsTo(DoctorDate::class);

    }
}
