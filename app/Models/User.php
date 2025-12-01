<?php

namespace App\Models;

use App\Casts\AsPhoneNumber;
use App\Mail\PasswordResetMail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Mail\Mailer;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Uri;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $full_name
 * @property string $email
 * @property string $password
 * @property \App\ValueObjects\PhoneNumber $phone_number
 * @property \Carbon\Carbon $email_verified_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class User extends Authenticatable implements CanResetPassword, JWTSubject {
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'phone_number_country_code',
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
     * Create a new Eloquent model instance.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(array $attributes, private readonly Application $application) {
        parent::__construct($attributes);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'phone_number' => AsPhoneNumber::class,
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier(): mixed {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array<string, mixed>
     */
    public function getJWTCustomClaims(): array {
        return [];
    }

    /**
     * Send a password reset notification to the user.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void {
        $configRepository = $this->application->make(Repository::class);

        $mailer = $this->application->make(Mailer::class);

        $frontURI = Uri::of($configRepository->string('app.frontend_url'))
            ->withPath('reset-password')
            ->withQuery(['email' => $this->email, 'token' => $token])
            ->toStringable()
            ->toString();

        $count = $configRepository->integer('auth.passwords.' . $configRepository->string('auth.defaults.passwords') . '.expire');

        $mailer->to($this->email)->send(new PasswordResetMail($frontURI, $count));
    }

    /**
     * @return Attribute<string, never>
     */
    protected function fullName(): Attribute {
        return Attribute::make(
            get: fn (): string => trim((string) $this->first_name . ' ' . (string) $this->last_name),
        );
    }

    /**
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo {
        return $this->belongsTo(Country::class);
    }
}
