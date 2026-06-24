<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicCalendar extends Model
{
    use HasFactory;

    protected $table = 'academic_calendar';

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'type',
        'description'
    ];
}
