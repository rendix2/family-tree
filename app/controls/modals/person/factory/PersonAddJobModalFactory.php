<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddJobModalFactory.php
 * User: Tomáš Babický
 * Date: 30.03.2021
 * Time: 11:16
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddJobModal;

/**
 * Interface PersonAddJobModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddJobModalFactory
{
    /**
     * @return PersonAddJobModal
     */
    public function create();
}
