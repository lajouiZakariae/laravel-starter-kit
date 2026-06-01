<?php

namespace App\Models;

use App\Casts\AsPhoneNumber;
use App\Mail\PasswordResetMail;
use App\ValueObjects\PhoneNumber;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Mail\Mailer;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Uri;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $full_name
 * @property string $email
 * @property string $password
 * @property ?PhoneNumber $phone_number
 * @property Carbon $email_verified_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable([
    'first_name',
    'last_name',
    'email',
    'password',
    'phone_number',
    'phone_number_country_code',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable implements CanResetPassword, JWTSubject {
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;

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
     * This method is used to send Link based password reset emails to users.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void {
        $configRepository = App::make(Repository::class);

        $mailer = App::make(Mailer::class);

        $frontURI = Uri::of($configRepository->get('app.frontend_url'))
            ->withPath('reset-password')
            ->withQuery(['email' => $this->email, 'token' => $token])
            ->toStringable()
            ->toString();

        $count = $configRepository->get('auth.passwords.' . $configRepository->get('auth.defaults.passwords') . '.expire');

        $mail = (new PasswordResetMail($frontURI, $count))->onQueue('user-emails-queue');

        $mailer->to($this->email)->queue($mail);
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
