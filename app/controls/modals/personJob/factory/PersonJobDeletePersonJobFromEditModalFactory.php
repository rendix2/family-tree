<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonJobDeletePersonJobFromEditModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:02
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\PersonJob\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\PersonAddress\PersonAddressDeletePersonAddressFromListModal;

/**
 * Interface PersonJobDeletePersonJobFromEditModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\PersonJob\Factory
 */
interface PersonJobDeletePersonJobFromEditModalFactory
{
    /**
     * @return PersonAddressDeletePersonAddressFromListModal
     */
    public function create();
}
