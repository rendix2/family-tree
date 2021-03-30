<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddParentMaleModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:59
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddParentPartnerMaleModal;

/**
 * Interface PersonAddParentPartnerMaleModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddParentPartnerMaleModalFactory
{
    /**
     * @return PersonAddParentPartnerMaleModal
     */
    public function create();
}