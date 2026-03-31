<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDate extends Model
{
    use HasFactory;
    
        protected $fillable = [
        'uuid',
        'from_hour',
        'to_hour',
        'day',
        'employee_id',
        'number_of_patients',
        'number_of_workDay',
        'number_of_booking',
        'number_of_operations'
        
    ];
    
    public function employee():object
    {
        return $this->HasOne(Employee::class);

    }
}
