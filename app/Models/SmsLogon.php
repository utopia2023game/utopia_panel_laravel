<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmsLogon extends Model
{
    use HasFactory, Notifiable;


    protected $table = 'sms_logons';

    protected $guarded = [
        'id',
    ];

}