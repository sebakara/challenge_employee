<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';
    protected $fillable = ['employee_id,start_time,end_time'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
