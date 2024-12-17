<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use HasFactory;
    protected $fillable = ['req_name', 'service_id'];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
