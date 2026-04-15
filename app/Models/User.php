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
            'point_expires_at' => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::retrieved(function ($user) {
            // Lazy check for point expiration
            if ($user->point_expires_at && $user->point_expires_at->isPast() && $user->points > 0) {
                $user->points = 0;
                $user->point_expires_at = null;
                
                // Also update card number if it exists
                if ($user->card_number) {
                    $user->card_number = static::generateCardNumber($user);
                }

                $user->saveQuietly();
            }
        });

        static::created(function ($user) {
            if (($user->role === 'member' || !$user->role) && !$user->is_admin && !$user->card_number) {
                $user->card_number = static::generateCardNumber($user);
                $user->save(['timestamps' => false]);
            }
        });

        static::saving(function ($user) {
            // Only for members
            if (($user->role === 'member' || !$user->role) && !$user->is_admin) {
                $newCardNumber = static::generateCardNumber($user);
                
                // If it's the first time or if the tier/data changed resulting in a new number
                // We compare the tier part (Blok 3, digit 1)
                if (!$user->card_number) {
                    $user->card_number = $newCardNumber;
                } else {
                    $oldTier = substr(str_replace(' ', '', $user->getOriginal('card_number')), 8, 1);
                    $newTier = substr(str_replace(' ', '', $newCardNumber), 8, 1);
                    
                    if ($oldTier !== $newTier) {
                        // Preserving the old random part if we want, or just generate new one.
                        // Let's generate a fresh one for the new "version" of the card
                        $user->card_number = $newCardNumber;
                    }
                }
            }
        });
    }

    public static function generateCardNumber($user)
    {
        $prefix = "4231";
        
        // Use registration date (created_at) or current date if user isn't saved yet
        $date = ($user->created_at ?? now())->format('ym');
        
        // Tier code: bronze=1, silver=2, gold=3
        $tierCode = "1";
        $points = $user->points ?? 0;
        
        // Thresholds based on PicPic Cafe rules
        if ($points >= 1501) $tierCode = "3";
        elseif ($points >= 501) $tierCode = "2";

        $idPart = str_pad($user->id ?? 0, 3, '0', STR_PAD_LEFT);
        
        // If we have an existing card number, we might want to preserve the random part
        // for "upgraded" cards, but a new number is also standard. 
        // Let's generate a new random part to keep it fresh.
        $randomPart = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        return "{$prefix} {$date} {$tierCode}{$idPart} {$randomPart}";
    }
}
