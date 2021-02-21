<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceAddSourceTypeModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:04
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Source\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Source\SourceAddSourceTypeModal;

/**
 * Interface SourceAddSourceTypeModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Source\Factory
 */
interface SourceAddSourceTypeModalFactory
{
    /**
     * @return SourceAddSourceTypeModal
     */
    public function create();
}