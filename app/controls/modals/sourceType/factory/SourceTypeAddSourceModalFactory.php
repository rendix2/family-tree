<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeAddSourceModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:05
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\SourceType\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\SourceType\SourceTypeAddSourceModal;

interface SourceTypeAddSourceModalFactory
{
    /**
     * @return SourceTypeAddSourceModal
     */
    public function create();
}