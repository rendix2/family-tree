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
 * Class Person2AddressManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class Person2AddressManager extends M2NManager
{
    /**
     * Person2AddressManager constructor.
     * @param Connection $dibi
     * @param PersonManager $left
     * @param AddressManager $right
     */
    public function __construct(Connection $dibi, PersonManager $left, AddressManager $right)
    {
        parent::__construct($dibi, $left, $right);
    }
}
