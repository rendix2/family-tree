<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusModalContainer.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 16:32
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Genus\Container;

use Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory\GenusAddNameModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory\GenusDeleteGenusFromEditModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory\GenusDeleteGenusFromListModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory\GenusDeletePersonGenusModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory\GenusDeletePersonNameModalFactory;

/**
 * Class GenusModalContainer
 * @package Rendix2\FamilyTree\App\Controls\Modals\Genus\Container
 */
class GenusModalContainer
{
    /**
     * @var GenusAddNameModalFactory $genusAddNameModalFactory
     */
    private $genusAddNameModalFactory;

    /**
     * @var GenusDeleteGenusFromEditModalFactory $genusDeleteGenusFromEditModalFactory
     */
    private $genusDeleteGenusFromEditModalFactory;

    /**
     * @var GenusDeleteGenusFromListModalFactory $genusDeleteGenusFromListModalFactory
     */
    private $genusDeleteGenusFromListModalFactory;

    /**
     * @var GenusDeletePersonGenusModalFactory $genusDeletePersonGenusModalFactory
     */
    private $genusDeletePersonGenusModalFactory;

    /**
     * @var GenusDeletePersonNameModalFactory $genusDeletePersonNameModalFactory
     */
    private $genusDeletePersonNameModalFactory;

    /**
     * GenusModalContainer constructor.
     * @param GenusAddNameModalFactory $genusAddNameModalFactory
     * @param GenusDeleteGenusFromEditModalFactory $genusDeleteGenusFromEditModalFactory
     * @param GenusDeleteGenusFromListModalFactory $genusDeleteGenusFromListModalFactory
     * @param GenusDeletePersonGenusModalFactory $genusDeletePersonGenusModalFactory
     * @param GenusDeletePersonNameModalFactory $genusDeletePersonNameModalFactory
     */
    public function __construct(
        GenusAddNameModalFactory $genusAddNameModalFactory,
        GenusDeleteGenusFromEditModalFactory $genusDeleteGenusFromEditModalFactory,
        GenusDeleteGenusFromListModalFactory $genusDeleteGenusFromListModalFactory,
        GenusDeletePersonGenusModalFactory $genusDeletePersonGenusModalFactory,
        GenusDeletePersonNameModalFactory $genusDeletePersonNameModalFactory
    ) {
        $this->genusAddNameModalFactory = $genusAddNameModalFactory;
        $this->genusDeleteGenusFromEditModalFactory = $genusDeleteGenusFromEditModalFactory;
        $this->genusDeleteGenusFromListModalFactory = $genusDeleteGenusFromListModalFactory;
        $this->genusDeletePersonGenusModalFactory = $genusDeletePersonGenusModalFactory;
        $this->genusDeletePersonNameModalFactory = $genusDeletePersonNameModalFactory;
    }

    /**
     * @return GenusAddNameModalFactory
     */
    public function getGenusAddNameModalFactory()
    {
        return $this->genusAddNameModalFactory;
    }

    /**
     * @return GenusDeleteGenusFromEditModalFactory
     */
    public function getGenusDeleteGenusFromEditModalFactory()
    {
        return $this->genusDeleteGenusFromEditModalFactory;
    }

    /**
     * @return GenusDeleteGenusFromListModalFactory
     */
    public function getGenusDeleteGenusFromListModalFactory()
    {
        return $this->genusDeleteGenusFromListModalFactory;
    }

    /**
     * @return GenusDeletePersonGenusModalFactory
     */
    public function getGenusDeletePersonGenusModalFactory()
    {
        return $this->genusDeletePersonGenusModalFactory;
    }

    /**
     * @return GenusDeletePersonNameModalFactory
     */
    public function getGenusDeletePersonNameModalFactory()
    {
        return $this->genusDeletePersonNameModalFactory;
    }
}
