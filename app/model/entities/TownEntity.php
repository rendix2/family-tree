<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownEntity.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 3:28
 */

namespace Rendix2\FamilyTree\App\Model\Entities;

/**
 * Class TownEntity
 *
 * @property int _countryId
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
class TownEntity implements IEntity
{
    use Entity;
    use PrimaryKey;

    /**
     * @var CountryEntity $country
     */
    public $country;

    /**
     * @var string $name
     */
    public $name;

    /**
     * @var string $zipCode
     */
    public $zipCode;

    /**
     * @var string $gps
     */
    public $gps;
}
