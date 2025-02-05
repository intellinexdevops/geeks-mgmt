<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = "attendance";
    protected $fillable = [
        'employee_id',
        'date',
        'status',
        'check_in_time',
        'check_out_time'
    ];
}