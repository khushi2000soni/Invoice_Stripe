<?php

namespace App\Models;

//use App\Traits\SpatiePermissionTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory;

    public $table = 'permissions';

    protected $dates = [
        'created_at',
        'updated_at',
        // 'deleted_at',
    ];

    protected $fillable = [
        'name',
        'guard_name',
        'route_name',
        'created_at',
        'updated_at',
        // 'deleted_at',
    ];


    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions');
    }
}
