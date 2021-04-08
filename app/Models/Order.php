<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $primaryKey = 'order_id';

    public function orderstatuss()
    {
        return $this->belongsTo('App\Models\OrderStatus', 'order_statusc', 'order_status_id');
    }
}
