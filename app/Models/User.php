<?php

namespace App\Models;

use App\Notifications\OtpSendNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guard = 'web';

    public $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'username',
        'phone',
        'auth_pin',
        'address',
        'address_id',
        'password',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token',
        'is_active',
        'email_verified_at',
        'otp',
        'subject',
        'expiretime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function address(){
        return $this->belongsTo(Address::class, 'address_id','id');
    }

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function profileImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'profile');
    }

    public function getProfileImageUrlAttribute()
    {
        if ($this->profileImage) {
            return $this->profileImage->file_url;
        }
        return "";
    }

    public function device()
    {
        return $this->hasOne(Device::class,'staff_id','id');
    }

    public function sendPasswordResetOtpNotification($request, $user)
    {
        $this->notify(new OtpSendNotification($user));
    }

    public function routeNotificationForMail()
    {
        return $this->email;
    }
}
