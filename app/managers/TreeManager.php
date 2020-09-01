<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TreeManager.php
 * User: Tomáš Babický
 * Date: 27.08.2020
 * Time: 16:15
 */

namespace Rendix2\FamilyTree\App\Managers;

/**
 * Class TreeManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class TreeManager
{
    /**
     * @var PeopleManager $peopleManager
     */
    private $peopleManager;

    /**
     * @var NameManager $nameManager
     */
    private $nameManager;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * TreeManager constructor.
     *
     * @param PeopleManager $peopleManager
     * @param NameManager $nameManager
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        PeopleManager $peopleManager,
        NameManager $nameManager,
        WeddingManager $weddingManager
    ) {
        $this->peopleManager = $peopleManager;
        $this->nameManager = $nameManager;
        $this->weddingManager = $weddingManager;
    }

    /**
     * @return array
     */
    public function getTree()
    {
        $peoples = $this->peopleManager->get();

        foreach ($peoples as $people) {
            $lastWedding  = $this->weddingManager->getLastByWifeId($people->id);
            $namesArray = $this->nameManager->getByPeopleId($people->id);
            $names = [];

            // set names
            foreach ($namesArray as $name) {
                $names[] = $name->name . ' ' . $name->surname . ' ('.date_format($name->dateSince, 'd.m.Y').' - '.date_format($name->dateTo, 'd.m.Y').')';
            }


            $people->names = $names;

            // set partner
            if ($lastWedding) {
                $people->tags = ['partner'];
                $people->pid = $lastWedding->husbandId;
            }
            
            // set parents
            if (
                !isset($people->ppid) &&
                $people->motherId !== null &&
                $people->fatherId !== null &&
                !isset($people->pid)
            ) {
                $people->pid = $people->fatherId;
                $people->ppid = $people->motherId;
            }

            unset($people->fatherId, $people->motherId);
        }

        return $peoples;
    }
}
