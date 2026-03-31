<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IllnessMedicine extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'name',
        'price',
        'minage',
        'maxage',
        'notes'
        
    ];
    
    public function illness():object{
        return $this->BelongsToMany(Illness::class); 

    }
}
