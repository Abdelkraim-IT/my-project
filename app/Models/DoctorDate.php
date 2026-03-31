<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'from_hour',
        'to_hour',
        'day',
        'doctor_id',
        'number_of_patients',
        'number_of_workDay',
        'number_of_booking',
        'number_of_operations'
        
    ];
    
    public function doctor():object
    {
        return $this->HasOne(Doctor::class);

    }
}
