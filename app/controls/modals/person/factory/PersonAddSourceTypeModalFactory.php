<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddSourceTypeModalFactory.php
 * User: Tomáš Babický
 * Date: 30.03.2021
 * Time: 10:41
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddSourceTypeModal;

/**
 * Interface PersonAddSourceTypeModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddSourceTypeModalFactory
{
    /**
     * @return PersonAddSourceTypeModal
     */
    public function create();
}
