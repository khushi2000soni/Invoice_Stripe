<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Device extends Model
{
    use HasFactory,HasApiTokens,Notifiable,SoftDeletes;
    public $table = 'devices';

    protected $fillable = [
        'name',
        'staff_id',
        'device_id',
        'device_ip',
        'pin',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function(Device $model) {
            $model->created_by = auth()->user()->id;
        });

        static::deleting(function(Device $model) {
            $model->deleted_by = auth()->user()->id;
            $model->save();
        });

        static::updating(function(Device $model) {
            $model->updated_by = auth()->user()->id;
        });
    }

    public function staff()
    {
        return $this->belongsTo(User::class);
    }
}
