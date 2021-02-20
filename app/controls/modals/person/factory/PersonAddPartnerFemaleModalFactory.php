<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPartnerFemaleModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:00
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddPartnerFemaleModal;


/**
 * Interface PersonAddPartnerFemaleModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddPartnerFemaleModalFactory
{
    /**
     * @return PersonAddPartnerFemaleModal
     */
    public function create();
}