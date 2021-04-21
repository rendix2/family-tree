<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonUpdateService.php
 * User: Tomáš Babický
 * Date: 26.03.2021
 * Time: 2:52
 */

namespace Rendix2\FamilyTree\App\Services;

use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Facades\RelationFacade;
use Rendix2\FamilyTree\App\Model\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Presenters\PersonPresenter;

/**
 * Class PersonUpdateService
 *
 * @package Rendix2\FamilyTree\App\Services
 */
class PersonUpdateService
{
    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var RelationFacade $relationFacade
     */
    private $relationFacade;

    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * PersonUpdateService constructor.
     *
     * @param PersonManager $personManager
     * @param RelationFacade $relationFacade
     * @param WeddingFacade $weddingFacade
     */
    public function __construct(
        PersonManager $personManager,
        RelationFacade $relationFacade,
        WeddingFacade $weddingFacade
    ) {
        $this->personManager = $personManager;
        $this->relationFacade = $relationFacade;
        $this->weddingFacade = $weddingFacade;
    }

    public function __destruct()
    {
        $this->personManager = null;
        $this->relationFacade = null;
        $this->weddingFacade = null;
    }

    /**
     * @param PersonPresenter $presenter
     * @param int $personId
     */
    public function prepareWeddings(PersonPresenter $presenter, $personId)
    {
        $husbands = $this->weddingFacade->select()->getCachedManager()->getAllByWifeId($personId);
        $wives = $this->weddingFacade->select()->getCachedManager()->getAllByHusbandId($personId);

        $presenter->template->wives = $wives;
        $presenter->template->husbands = $husbands;
    }

    /**
     * @param PersonPresenter $presenter
     * @param int $personId
     */
    public function prepareRelations(PersonPresenter $presenter, $personId)
    {
        $femaleRelations = $this->relationFacade->select()->getCachedManager()->getByMaleId($personId);
        $maleRelations = $this->relationFacade->select()->getCachedManager()->getByFemaleId($personId);

        $presenter->template->maleRelations = $maleRelations;
        $presenter->template->femaleRelations = $femaleRelations;
    }

    /**
     * @param PersonPresenter $presenter
     * @param PersonEntity|null $father
     * @param PersonEntity|null $mother
     */
    public function prepareParentsRelations(PersonPresenter $presenter, $father = null, $mother = null)
    {
        $fathersRelations = [];
        $mothersRelations = [];

        if ($father && $mother) {
            $fathersRelations = $this->relationFacade->select()->getCachedManager()->getByMaleId($father->id);
            $mothersRelations = $this->relationFacade->select()->getCachedManager()->getByFemaleId($mother->id);
        } elseif ($father && !$mother) {
            $fathersRelations = $this->relationFacade->select()->getCachedManager()->getByMaleId($father->id);
        } elseif (!$father && $mother) {
            $mothersRelations = $this->relationFacade->select()->getCachedManager()->getByFemaleId($mother->id);
        }

        $presenter->template->fathersRelations = $fathersRelations;
        $presenter->template->mothersRelations = $mothersRelations;
    }

    /**
     * @param PersonPresenter $presenter
     * @param PersonEntity|null $father
     * @param PersonEntity|null $mother
     */
    public function prepareParentsWeddings(PersonPresenter $presenter, $father = null, $mother = null)
    {
        $fathersWeddings = [];
        $mothersWeddings = [];

        if ($father && $mother) {
            $fathersWeddings = $this->weddingFacade->select()->getCachedManager()->getAllByHusbandId($father->id);
            $mothersWeddings = $this->weddingFacade->select()->getCachedManager()->getAllByWifeId($mother->id);
        } elseif ($father && !$mother) {
            $fathersWeddings = $this->weddingFacade->select()->getCachedManager()->getAllByHusbandId($father->id);
        } elseif (!$father && $mother) {
            $mothersWeddings = $this->weddingFacade->select()->getCachedManager()->getAllByWifeId($mother->id);
        }

        $presenter->template->fathersWeddings = $fathersWeddings;
        $presenter->template->mothersWeddings = $mothersWeddings;
    }

    /**
     * @param PersonPresenter $presenter
     * @param int $id
     * @param PersonEntity|null $father
     * @param PersonEntity|null $mother
     */
    public function prepareBrothersAndSisters(PersonPresenter $presenter, $id, $father = null, $mother = null)
    {
        $brothers = [];
        $sisters = [];

        if ($father && $mother) {
            $brothers = $this->personManager->select()->getSettingsCachedManager()->getBrothers($father->id, $mother->id, $id);
            $sisters = $this->personManager->select()->getSettingsCachedManager()->getSisters($father->id, $mother->id, $id);
        } elseif ($father && !$mother) {
            $brothers = $this->personManager->select()->getSettingsCachedManager()->getBrothers($father->id, null, $id);
            $sisters = $this->personManager->select()->getSettingsCachedManager()->getSisters($father->id, null, $id);
        } elseif (!$father && $mother) {
            $brothers = $this->personManager->select()->getSettingsCachedManager()->getBrothers(null, $mother->id, $id);
            $sisters = $this->personManager->select()->getSettingsCachedManager()->getSisters(null, $mother->id, $id);
        }

        $presenter->template->brothers = $brothers;
        $presenter->template->sisters = $sisters;
    }
}
