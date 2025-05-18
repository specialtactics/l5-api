<?php
namespace Specialtactics\L5Api\Enums;

/**
 * This denotes the type of Laravel pagination to use, for example in controllers
 *
 * By default, and for compatibility reasons with previous versions of the boilerplate, we will use LengthAware pagination
 */
enum PaginationType: string
{
    case SIMPLE = 'simple';
    case LENGTH_AWARE = 'length-aware';
    case CURSOR = 'cursor';

    /**
     * Attempt to get enum from string value, and set a default if we can't.
     */
    public static function getFromValue(?string $value): self
    {
        if (! $value) {
            return self::LENGTH_AWARE;
        }

        return self::tryFrom($value) ?? self::LENGTH_AWARE;
    }
}