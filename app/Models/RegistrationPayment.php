<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationPayment extends Model
{
    use HasFactory;
    protected $table = 'registration_payments';
    protected $primaryKey = 'id';
    protected $timestamp = true;
    protected $guarded = [];
}
