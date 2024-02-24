<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreDefinedMessage extends Model
{
    protected $table = 'pre_defined_message';
        
    protected $fillable = ['name','status','created_at','updated_at','created_by','updated_by'];
}