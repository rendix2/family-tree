<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddressModalContainer.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:44
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\PersonAddress\Container;

use Rendix2\FamilyTree\App\Controls\Modals\PersonAddress\Factory\PersonAddressDeletePersonAddressFromEditModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\PersonAddress\Factory\PersonAddressDeletePersonAddressFromListModalFactory;

/**
 * Class PersonAddressModalContainer
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\PersonAddress\Container
 */
class PersonAddressModalContainer
{
    /**
     * @var PersonAddressDeletePersonAddressFromEditModalFactory $personAddressDeletePersonAddressFromEditModalFactory
     */
    private $personAddressDeletePersonAddressFromEditModalFactory;

    /**
     * @var PersonAddressDeletePersonAddressFromListModalFactory $personAddressDeletePersonAddressFromListModalFactory
     */
    private $personAddressDeletePersonAddressFromListModalFactory;

    /**
     * PersonAddressModalContainer constructor.
     * @param PersonAddressDeletePersonAddressFromEditModalFactory $personAddressDeletePersonAddressFromEditModalFactory
     * @param PersonAddressDeletePersonAddressFromListModalFactory $personAddressDeletePersonAddressFromListModalFactory
     */
    public function __construct(
        PersonAddressDeletePersonAddressFromEditModalFactory $personAddressDeletePersonAddressFromEditModalFactory,
        PersonAddressDeletePersonAddressFromListModalFactory $personAddressDeletePersonAddressFromListModalFactory
    ) {
        $this->personAddressDeletePersonAddressFromEditModalFactory = $personAddressDeletePersonAddressFromEditModalFactory;
        $this->personAddressDeletePersonAddressFromListModalFactory = $personAddressDeletePersonAddressFromListModalFactory;
    }

    /**
     * @return PersonAddressDeletePersonAddressFromEditModalFactory
     */
    public function getPersonAddressDeletePersonAddressFromEditModalFactory()
    {
        return $this->personAddressDeletePersonAddressFromEditModalFactory;
    }

    /**
     * @return PersonAddressDeletePersonAddressFromListModalFactory
     */
    public function getPersonAddressDeletePersonAddressFromListModalFactory()
    {
        return $this->personAddressDeletePersonAddressFromListModalFactory;
    }
}