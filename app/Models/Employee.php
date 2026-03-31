<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'image',
        'ID_number',
        'status',
        'password',
        'phone',
        'job',
        'salary',  
    ];
        public function EmployeeDate():object
    {
        return $this->BelongsTo(EmployeeDate::class);

    }

}
