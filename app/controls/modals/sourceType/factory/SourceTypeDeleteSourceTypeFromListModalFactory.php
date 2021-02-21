<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeDeleteSourceTypeFromListModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:06
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\SourceType\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\SourceType\SourceTypeDeleteSourceTypeFromListModal;

interface SourceTypeDeleteSourceTypeFromListModalFactory
{
    /**
     * @return SourceTypeDeleteSourceTypeFromListModal
     */
    public function create();
}