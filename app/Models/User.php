<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomResetPasswordNotification;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $nivel
 * @property int|null $fk_id_empresa
 * @property int|null $fk_id_estagiario
 * @property string|null $email_verification_token
 * @property \Illuminate\Support\Carbon|null $email_verification_expires_at
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nivel',
        'fk_id_empresa',
        'fk_id_estagiario',
        'senha',
        'email_verification_token',
        'email_verification_expires_at',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'email_verification_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresa');
    }

    public function validatePassword(string $password): string
    {
        //validações
        $lowercase = preg_match('/[a-z]/', $password);
        $uppercase = preg_match('/[A-Z]/', $password);
        $number = preg_match('/[0-9]/', $password);
        $specialChars = preg_match('/[^\w]/', $password);

        //dd($lowercase, $uppercase, $number, $specialChars);

        if (!$lowercase) {
            throw ValidationException::withMessages(['password' => 'A senha deve conter pelo menos uma letra minúscula']);
        } elseif (!$uppercase) {
            throw ValidationException::withMessages(['password' => 'A senha deve conter pelo menos uma letra maiúscula']);
        } elseif (!$number) {
            throw ValidationException::withMessages(['password' => 'A senha deve conter pelo menos um número']);
        } elseif (!$specialChars) {
            throw ValidationException::withMessages(['password' => 'A senha deve conter pelo menos um caractere especial']);
        }

        return $password;
    }

    public function generateEmailVerificationCode(): string
    {
        // 6 dígitos numéricos
        return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function startEmailVerification(): string
    {
        $code = $this->generateEmailVerificationCode();
        // Armazena hash do código e expiração em 15 minutos
        $this->email_verification_token = password_hash($code, PASSWORD_BCRYPT);
        $this->email_verification_expires_at = now()->addMinutes(15);
        $this->save();
        return $code;
    }

    public function checkEmailVerificationCode(string $code): bool
    {
        if (!$this->email_verification_token || !$this->email_verification_expires_at) {
            return false;
        }
        if (now()->greaterThan($this->email_verification_expires_at)) {
            return false;
        }
        return password_verify($code, $this->email_verification_token);
    }

    /**
     * Envia notificação customizada de redefinição de senha.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }
}
