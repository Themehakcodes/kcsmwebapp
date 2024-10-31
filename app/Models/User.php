<?php

namespace App\Models;

use \DateTimeInterface;
use Carbon\Carbon;
use Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable 
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;

    use Notifiable;
    use TwoFactorAuthenticatable;
    //use InteractsWithMedia;

    protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'is_admin',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    // public function getIsAdminAttribute()
    // {
    //     return $this->roles()->where('id', 1)->exists();
    // }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function roles()
{
    return $this->belongsToMany(Role::class, 'role_users')
                ->whereNull('role_users.deleted_at') // Ensure soft-deleted pivot entries are excluded
                ->whereNull('roles.deleted_at'); // Ensure soft-deleted roles are excluded
}

    

    
    

    public function getProfileAttribute()
    {
        $file = $this->getMedia('profile')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }

        return $file;
    }
    public function hasPermission($permission)
    {
        return $this->roles()
            ->join('permission_roles', function ($join) {
                $join->on('roles.id', '=', 'permission_roles.role_id')
                     ->whereNull('permission_roles.deleted_at'); // Ensure permission_roles is not deleted
            })
            ->join('permissions', function ($join) {
                $join->on('permission_roles.permission_id', '=', 'permissions.id')
                     ->whereNull('permissions.deleted_at'); // Ensure permissions is not deleted
            })
            ->where('permissions.title', $permission) // Check for the specific permission title
            ->exists(); // Check for existence directly in the database
    }
    

    
    

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
