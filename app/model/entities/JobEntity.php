<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobEntity.php
 * User: Tomáš Babický
 * Date: 15.11.2020
 * Time: 23:59
 */

namespace Rendix2\FamilyTree\App\Model\Entities;

/**
 * Class JobEntity
 *
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
class JobEntity implements IEntity
{
    use Entity;
    use PrimaryKey;

    /**
     * @var string $company
     */
    public $company;

    /**
     * @var string $position
     */
    public $position;

    /**
     * @var TownEntity $town
     */
    public $town;

    /**
     * @var AddressEntity $address
     */
    public $address;
}
