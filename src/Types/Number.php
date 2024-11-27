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

use Underscore\Methods\NumberMethods;
use Underscore\Traits\Repository;

/**
 * Number repository.
 *
 * @mixin NumberMethods
 */
class Number extends Repository
{
    /**
     * The method used to convert new subjects.
     */
    protected string $typecaster = 'toInteger';

    /**
     * Get a default value for a new repository.
     */
    protected function getDefault() : int
    {
        return 0;
    }
}
