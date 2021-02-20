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
class CountryEntity implements IEntity
{
    use Entity;
    use PrimaryKey;

    /**
     * @var string $name
     */
    public $name;
}
