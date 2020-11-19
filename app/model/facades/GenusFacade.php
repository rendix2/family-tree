<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusFacade.php
 * User: Tomáš Babický
 * Date: 15.11.2020
 * Time: 1:57
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Entities\GenusEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;

/**
 * Class GenusFacade
 *
 * @package Rendix2\FamilyTree\App\Model\Facades
 */
class GenusFacade
{
    /**
     * @var GenusManager
     */
    private $genusManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * GenusFacade constructor.
     *
     * @param GenusManager $genusManager
     * @param PersonManager $personManager
     */
    public function __construct(
        GenusManager $genusManager,
        PersonManager $personManager
    ) {
        $this->genusManager = $genusManager;
        $this->personManager = $personManager;
    }

    /**
     * @param GenusEntity[] $genuses
     * @param PersonEntity[] $persons
     */
    public function join(array $genuses, array $persons)
    {
        foreach ($genuses as $genus) {
            foreach ($persons as $person) {
                if ($genus->_personId === $person->id) {
                    $genus->person = $person;
                    break;
                }
            }

            $genus->clean();
        }
    }
}
