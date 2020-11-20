<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Person2JobEntity.php
 * User: Tomáš Babický
 * Date: 15.11.2020
 * Time: 23:58
 */

namespace Rendix2\FamilyTree\App\Model\Entities;

/**
 * Class Person2JobEntity
 *
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
class Person2JobEntity
{
    use Construct;

    /**
     * @var PersonEntity $person
     */
    public $person;

    /**
     * @var JobEntity $job
     */
    public $job;

    /**
     * @var DurationEntity $duration
     */
    public $duration;
}
