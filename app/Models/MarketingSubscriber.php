<?php

namespace App\Models;

use Database\Factories\MarketingSubscriberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingSubscriber extends Model
{
    /** @use HasFactory<MarketingSubscriberFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'name',
        'source',
        'ip_address',
    ];

    /**
     * Safely subscribe an email address if it does not already exist.
     * Never throws exceptions to ensure uninterrupted user registration flows.
     */
    public static function subscribe(?string $email, ?string $name = null, string $source = 'unknown', ?string $ipAddress = null): ?self
    {
        if (empty($email)) {
            return null;
        }

        $cleanEmail = mb_strtolower(trim($email));

        if (! filter_var($cleanEmail, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        try {
            if (static::where('email', $cleanEmail)->exists()) {
                return null;
            }

            return static::create([
                'email' => $cleanEmail,
                'name' => $name !== null ? trim($name) : null,
                'source' => $source,
                'ip_address' => $ipAddress,
            ]);
        } catch (\Throwable) {
            // Silently ignore any collision or database hiccups to keep the user flow intact
            return null;
        }
    }
}
