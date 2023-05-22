<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $table = 'postsmeta';

    protected $fillable = [
        'post_id',
        'key',
        'value',
    ];

    public $timestamps = false;
}
