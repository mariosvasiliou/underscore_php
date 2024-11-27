<?php
declare(strict_types=1);

/*
 * This file is part of Underscore.php
 *
 * (c) Maxime Fabre <ehtnam6@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Underscore\Methods;

/**
 * Methods to manage objects.
 */
class BaseObjectMethods extends CollectionMethods
{

    /**
     * Get all methods from an object.
     *
     *
     */
    public static function methods(object $object) : array
    {
        return get_class_methods($object::class);
    }

    /**
     * Unpack an object's properties.
     *
     *
     */
    public static function unpack(object $object, mixed $attribute = null) : object
    {
        $object = (array) $object;
        $object = $attribute
            ? ArraysMethods::get($object, $attribute)
            : ArraysMethods::first($object);

        return (object) $object;
    }
}
