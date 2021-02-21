<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceDeleteSourceFromListModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:05
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Source\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Source\SourceDeleteSourceFromListModal;

interface SourceDeleteSourceFromListModalFactory
{
    /**
     * @return SourceDeleteSourceFromListModal
     */
    public function create();
}