<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceDeleteSourceFromEditModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:04
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Source\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Source\SourceDeleteSourceFromEditModal;

/**
 * Interface SourceDeleteSourceFromEditModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Source\Factory
 */
interface SourceDeleteSourceFromEditModalFactory
{
    /**
     * @return SourceDeleteSourceFromEditModal
     */
    public function create();
}
