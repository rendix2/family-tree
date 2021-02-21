<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeDeleteSourceTypeFromEditModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:06
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\SourceType\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\SourceType\SourceTypeDeleteSourceTypeFromEditModal;

interface SourceTypeDeleteSourceTypeFromEditModalFactory
{
    /**
     * @return SourceTypeDeleteSourceTypeFromEditModal
     */
    public function create();
}