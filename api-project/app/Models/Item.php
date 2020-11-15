<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'items';
    protected $fillable = ['name','description','auctionDeadline','price','highestBid','biddingHistory'];
    
    protected $casts = [
        'highestBid' => 'array',
    ];
    use HasFactory;
}
