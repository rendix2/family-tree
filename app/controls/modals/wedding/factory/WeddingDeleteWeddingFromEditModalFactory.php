<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingDeleteWeddingFromEditModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:08
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Wedding\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Wedding\WeddingDeleteWeddingFromEditModal;

interface WeddingDeleteWeddingFromEditModalFactory
{
    /**
     * @return WeddingDeleteWeddingFromEditModal
     */
    public function create();
}