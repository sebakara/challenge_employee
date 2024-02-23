<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $table = 'employees';
    protected $fillable = ['user_id','emp_id','phone_number'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
