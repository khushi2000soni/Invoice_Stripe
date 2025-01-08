<?php

namespace App\Models;

use App\Traits\SpatieRoleTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasPermissions;

//use Illuminate\Database\Eloquent\SoftDeletes;


class Role extends Model
{
    use HasFactory, HasPermissions;

    public $table = 'roles';
    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at',
        // 'deleted_at',
    ];

    protected $fillable = [
        'name',
        'guard_name',
        'created_at',
        'updated_at',
        // 'deleted_at',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);

    }


}
