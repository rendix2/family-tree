<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationEntity.php
 * User: Tomáš Babický
 * Date: 15.11.2020
 * Time: 1:21
 */

namespace Rendix2\FamilyTree\App\Model\Entities;

/**
 * Class RelationEntity
 *
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
class RelationEntity
{
    use Entity;

    /**
     * @var int $id
     */
    public $id;

    /**
     * @var PersonEntity $male
     */
    public $male;

    /**
     * @var PersonEntity $female
     */
    public $female;

    /**
     * @var DurationEntity $duration
     */
    public $duration;
}