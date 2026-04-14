<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, \Illuminate\Database\Eloquent\SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'points',
        'avatar',
        'phone',
        'is_admin',
        'role',
        'card_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    protected static function booted()
    {
        static::created(function ($user) {
            // Only generate card number for regular members (not admin/cashier/owner)
            // And only if it doesn't have one yet
            if (($user->role === 'member' || !$user->role) && !$user->is_admin && !$user->card_number) {
                $user->card_number = static::generateCardNumber($user);
                $user->save(['timestamps' => false]);
            }
        });
    }

    public static function generateCardNumber($user)
    {
        $prefix = "4231";
        $date = now()->format('ym'); // 2604
        
        // Tier code: bronze=1, silver=2, gold=3
        $tierCode = "1";
        $points = $user->points ?? 0;
        
        // Get loyalty settings if possible, or use hardcoded thresholds
        if ($points >= 1501) $tierCode = "3";
        elseif ($points >= 501) $tierCode = "2";

        $idPart = str_pad($user->id ?? 0, 3, '0', STR_PAD_LEFT);
        $randomPart = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        return "{$prefix} {$date} {$tierCode}{$idPart} {$randomPart}";
    }
}
