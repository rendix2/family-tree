<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryEntity.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 3:30
 */

namespace Rendix2\FamilyTree\App\Model\Entities;

/**
 * Class CountryEntity
 *
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
class CountryEntity
{
    use Construct;

    /**
     * @var int $id
     */
    public $id;

    /**
     * @var string $name
     */
    public $name;
}
