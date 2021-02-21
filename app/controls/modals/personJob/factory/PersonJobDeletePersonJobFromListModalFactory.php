<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonJobDeletePersonJobFromListModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:02
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\PersonJob\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\PersonJob\PersonJobDeletePersonJobFromListModal;

/**
 * Interface PersonJobDeletePersonJobFromListModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\PersonJob\Factory
 */
interface PersonJobDeletePersonJobFromListModalFactory
{
    /**
     * @return PersonJobDeletePersonJobFromListModal
     */
    public function create();
}
