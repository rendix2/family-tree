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
 * Class People2AddressManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class People2AddressManager extends M2NManager
{
    /**
     * People2AddressManager constructor.
     * @param Connection $dibi
     * @param PeopleManager $left
     * @param AddressManager $right
     *
     * @throws \Dibi\Exception
     */
    public function __construct(Connection $dibi, PeopleManager $left, AddressManager $right)
    {
        parent::__construct($dibi, $left, $right);
    }
}
