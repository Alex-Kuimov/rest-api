<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'created_at',
        'updated_at',
    ];

    public function meta()
    {
        return $this->hasMany(Meta::class);
    }
}