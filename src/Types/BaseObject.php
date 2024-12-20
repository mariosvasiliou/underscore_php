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

namespace Underscore\Types;

use stdClass;
use Underscore\Methods\BaseObjectMethods;
use Underscore\Traits\Repository;

/**
 * BaseObject repository.
 *
 * @mixin BaseObjectMethods
 */
class BaseObject extends Repository
{

    /**
     * The method used to convert new subjects.
     */
    protected string $typecaster = 'toObject';

    /**
     * Get a default value for a new repository.
     */
    protected function getDefault() : object
    {
        return new stdClass();
    }
}
