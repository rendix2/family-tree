<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeDeleteSourceModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:05
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\SourceType\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\SourceType\SourceTypeDeleteSourceModal;

interface SourceTypeDeleteSourceModalFactory
{
    /**
     * @return SourceTypeDeleteSourceModal
     */
    public function create();
}