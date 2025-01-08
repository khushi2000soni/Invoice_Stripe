<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Model
{
    use HasFactory,HasApiTokens,Notifiable,SoftDeletes;
    public $table = 'customers';

    protected $fillable = [
        'name',
        // 'email',
        'phone',
        'phone2',
        'guardian_name',
        'address_id',
        'password',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
        'updated_at',
        'deleted_at',
        'is_active',
        'is_verified',
    ];

    protected $appends = [
        'full_name'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function(Customer $model) {
            $model->created_by = auth()->user()->id;
        });

        static::deleting(function(Customer $model) {
            $model->deleted_by = auth()->user()->id;
            $model->save();
        });

        static::updating(function(Customer $model) {
            $model->updated_by = auth()->user()->id;
        });
    }


    public function address(){
        return $this->belongsTo(Address::class);
    }

    public function deletedByUser(){
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function setGuardianNameAttribute($value)
    {
        $this->attributes['guardian_name'] = $value ?? 'NA';
    }

    public function getFullNameAttribute()
    {
        $name = $this->name ?? 'NA';
        $guardianName = $this->guardian_name ?? 'NA';
        $address = $this->address ? $this->address->address : 'NA';
        return $name . '-' . $guardianName . '(' . $address . ')';
        //return $this->name . '-' . $this->guardian_name . '(' .$this->address->address .')';
    }


}
