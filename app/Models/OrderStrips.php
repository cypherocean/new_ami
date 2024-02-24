<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class OrderStrips extends Model{
        
        use HasFactory;

        protected $table = 'orders_strips';
        
        protected $fillable = ['order_id', 'strip_id', 'quantity', 'unit', 'choke', 'calc', 'price', 'remark', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
