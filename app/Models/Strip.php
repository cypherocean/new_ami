<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Strip extends Model{
        
        use HasFactory;

        protected $table = 'strips';
        
        protected $fillable = ['name', 'quantity', 'unit', 'choke', 'amp' ,'price', 'note', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
