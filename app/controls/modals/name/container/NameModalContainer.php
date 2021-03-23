<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameModalContainer.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 21:31
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Name\Container;

use Rendix2\FamilyTree\App\Controls\Modals\Name\Factory\NameAddGenusModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Name\Factory\NameDeleteNameFromEditModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Name\Factory\NameDeleteNameFromListModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Name\Factory\NameDeletePersonNameModalFactory;

/**
 * Class NameModalContainer
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Name\Container
 */
class NameModalContainer
{

    /**
     * @var NameAddGenusModalFactory
     */
    private $nameAddGenusModalFactory;

    /**
     * @var NameDeleteNameFromEditModalFactory
     */
    private $nameDeleteNameFromEditModalFactory;

    /**
     * @var NameDeleteNameFromListModalFactory
     */
    private $nameDeleteNameFromListModalFactory;

    /**
     * @var NameDeletePersonNameModalFactory
     */
    private $nameDeletePersonNameModalFactory;

    /**
     * NameModalContainer constructor.
     * @param NameAddGenusModalFactory $nameAddGenusModalFactory
     * @param NameDeleteNameFromEditModalFactory $nameDeleteNameFromEditModalFactory
     * @param NameDeleteNameFromListModalFactory $nameDeleteNameFromListModalFactory
     * @param NameDeletePersonNameModalFactory $nameDeletePersonNameModalFactory
     */
    public function __construct(
        NameAddGenusModalFactory $nameAddGenusModalFactory,
        NameDeleteNameFromEditModalFactory $nameDeleteNameFromEditModalFactory,
        NameDeleteNameFromListModalFactory $nameDeleteNameFromListModalFactory,
        NameDeletePersonNameModalFactory $nameDeletePersonNameModalFactory
    ) {
        $this->nameAddGenusModalFactory = $nameAddGenusModalFactory;
        $this->nameDeleteNameFromEditModalFactory = $nameDeleteNameFromEditModalFactory;
        $this->nameDeleteNameFromListModalFactory = $nameDeleteNameFromListModalFactory;
        $this->nameDeletePersonNameModalFactory = $nameDeletePersonNameModalFactory;
    }

    /**
     * @return NameAddGenusModalFactory
     */
    public function getNameAddGenusModalFactory()
    {
        return $this->nameAddGenusModalFactory;
    }

    /**
     * @return NameDeleteNameFromEditModalFactory
     */
    public function getNameDeleteNameFromEditModalFactory()
    {
        return $this->nameDeleteNameFromEditModalFactory;
    }

    /**
     * @return NameDeleteNameFromListModalFactory
     */
    public function getNameDeleteNameFromListModalFactory()
    {
        return $this->nameDeleteNameFromListModalFactory;
    }

    /**
     * @return NameDeletePersonNameModalFactory
     */
    public function getNameDeletePersonNameModalFactory()
    {
        return $this->nameDeletePersonNameModalFactory;
    }
}
