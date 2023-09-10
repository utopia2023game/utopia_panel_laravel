<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmsCustomerLogon extends Model
{
    use HasFactory, Notifiable , SoftDeletes;


    protected $table = 'sms_customer_logons';

    protected $guarded = [
        'id',
    ];

}