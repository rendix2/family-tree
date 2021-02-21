<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingDeleteWeddingFromListModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:08
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Wedding\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Wedding\WeddingDeleteWeddingFromListModal;

interface WeddingDeleteWeddingFromListModalFactory
{
    /**
     * @return WeddingDeleteWeddingFromListModal
     */
    public function create();
}