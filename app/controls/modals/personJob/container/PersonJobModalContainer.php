<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonJobModalContainer.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:55
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\PersonJob\Container;

use Rendix2\FamilyTree\App\Controls\Modals\PersonJob\Factory\PersonJobDeletePersonJobFromEditModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\PersonJob\Factory\PersonJobDeletePersonJobFromListModalFactory;

/**
 * Class PersonJobModalContainer
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\PersonJob\Container
 */
class PersonJobModalContainer
{
    /**
     * @var PersonJobDeletePersonJobFromEditModalFactory $personJobDeletePersonJobFromEditModalFactory
     */
    private $personJobDeletePersonJobFromEditModalFactory;

    /**
     * @var PersonJobDeletePersonJobFromListModalFactory $personJobDeletePersonJobFromListModalFactory
     */
    private $personJobDeletePersonJobFromListModalFactory;

    /**
     * PersonJobModalContainer constructor.
     * @param PersonJobDeletePersonJobFromEditModalFactory $personJobDeletePersonJobFromEditModalFactory
     * @param PersonJobDeletePersonJobFromListModalFactory $personJobDeletePersonJobFromListModalFactory
     */
    public function __construct(
        PersonJobDeletePersonJobFromEditModalFactory $personJobDeletePersonJobFromEditModalFactory,
        PersonJobDeletePersonJobFromListModalFactory $personJobDeletePersonJobFromListModalFactory
    ) {
        $this->personJobDeletePersonJobFromEditModalFactory = $personJobDeletePersonJobFromEditModalFactory;
        $this->personJobDeletePersonJobFromListModalFactory = $personJobDeletePersonJobFromListModalFactory;
    }

    /**
     * @return PersonJobDeletePersonJobFromEditModalFactory
     */
    public function getPersonJobDeletePersonJobFromEditModalFactory()
    {
        return $this->personJobDeletePersonJobFromEditModalFactory;
    }

    /**
     * @return PersonJobDeletePersonJobFromListModalFactory
     */
    public function getPersonJobDeletePersonJobFromListModalFactory()
    {
        return $this->personJobDeletePersonJobFromListModalFactory;
    }
}
