<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sections extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'name',
    ];

   
    public function class ()
    {
        return $this->belongsTo(Classes::class);
    }

    public function students(){
        return $this->hasMany(Student::class , 'section_id');
    }
}
