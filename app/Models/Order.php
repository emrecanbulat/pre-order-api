<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const STATUS_PENDING = "pending";
    use HasFactory;

    protected $fillable = ["user_id", "status"];
}
