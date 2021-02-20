<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPersonJobModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:03
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddPersonJobModal;

/**
 * Interface PersonAddPersonJobModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddPersonJobModalFactory
{
    /**
     * @return PersonAddPersonJobModal
     */
    public function create();
}
