<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameFIlter.php
 * User: Tomáš Babický
 * Date: 22.09.2020
 * Time: 20:19
 */

namespace Rendix2\FamilyTree\App\Filters;

use Dibi\Row;

/**
 * Class NameFilter
 * @package Rendix2\FamilyTree\App\Filters
 */
class NameFilter
{
    /**
     * @param Row $name
     *
     * @return string
     */
    public function __invoke(Row $name)
    {
        return $name->name . ' ' . $name->surname;
    }
}
