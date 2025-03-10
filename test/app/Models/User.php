<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use \Illuminate\Support\Facades\Hash;
use App\Models\Role;
use Database\Factories\UserFactory;

class User extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    JWTSubject
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable, HasFactory;

    /**
     * @var int Auto increments integer key
     */
    public $primaryKey = 'user_id';

    /**
     * @var ?array Relations to load implicitly by Restful controllers
     */
    public static ?array $itemWith = ['primaryRole', 'roles'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'primary_role'
    ];

    protected $casts = [
        'verifiable_until' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The attributes that should be hidden for arrays and API output
     */
    protected $hidden = [
        'password', 'remember_token', 'primary_role',
    ];

    /**
     * Model's boot function
     */
    public static function boot(): void
    {
        parent::boot();

        static::creating(function (User $user) {
            if (is_null($user->password)) {
                $user->password = Hash::make(Str::random(64));
            }
        });
    }

    protected static function newFactory(): UserFactory
    {
        return new UserFactory();
    }

    /**
     * Return the validation rules for this model
     *
     * @return array Rules
     */
    public function getValidationRules(): array
    {
        return [
            'email' => 'email|max:255|unique:users',
            'name'  => 'required|min:3',
            'password' => 'required|min:6',
        ];
    }

    /**
     * User's primary role
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function primaryRole() {
        return $this->belongsTo(Role::class, 'primary_role');
    }

    /**
     * User's secondary roles
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function roles() {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    /**
     * Get all user's roles
     */
    public function getRoles(): ?array
    {
        $allRoles = array_merge(
            [
                $this->primaryRole->name,
            ],
            $this->roles->pluck('name')->toArray()
        );

        return $allRoles;
    }

    /**
     * Is this user an admin?
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->primaryRole->name == Role::ROLE_ADMIN;
    }

    /**
     * For Authentication
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier(): string
    {
        return $this->getKey();
    }

    /**
     * For Authentication
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'user' => [
                'id' => $this->getKey(),
                'name' => $this->name,
                'primaryRole' => $this->primaryRole->name,
            ]
        ];
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return $this->getKeyName();
    }
}
