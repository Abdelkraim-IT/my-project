<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'from_date',
        'to_date',
        'type',
        'number_of_patients',
        'number_of_bookings'
        
    ];
}
