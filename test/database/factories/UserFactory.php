<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public const DEFAULT_PASSWORD = 'secret';

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    protected static ?Collection $allRoles = null;

    /**
     * Retrieve all roles
     */
    public static function getAllRoles(): Collection
    {
        if (is_null(static::$allRoles)) {
            static::$allRoles = Role::all();
        }

        return static::$allRoles;
    }

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => microtime(true) . '_' . $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::DEFAULT_PASSWORD,
            'remember_token' => Str::random(10),
            'active' => true,
        ];
    }

    public function adminRole(): UserFactory
    {
        return $this->state(function (array $attributes): array {
            return [
                'primary_role' => static::getAllRoles()
                    ->firstWhere('name', '=', Role::ROLE_ADMIN)
                    ->getKey(),
            ];
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): UserFactory
    {
        return $this->state(function (array $attributes): array {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
