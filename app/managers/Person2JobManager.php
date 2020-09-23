<?php
/**
 *
 * Created by PhpStorm.
 * Filename: s.php
 * User: Tomáš Babický
 * Date: 23.08.2020
 * Time: 15:11
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Connection;

/**
 * Class Person2JobManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class Person2JobManager extends M2NManager
{

    public function __construct(Connection $dibi, PersonManager $left, JobManager $right)
    {
        parent::__construct($dibi, $left, $right);
    }
}
