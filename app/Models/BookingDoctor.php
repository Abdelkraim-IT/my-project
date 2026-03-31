<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingDoctor extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'uuid',
        'doctor_dates_id',
        'patient_id',
        'date',
        
    ];
    public function created_at_difference()
    {
         return Carbon::createFromTimestamp(strtotime($this->date))->diff(Carbon::now())->days;
    } 
}
