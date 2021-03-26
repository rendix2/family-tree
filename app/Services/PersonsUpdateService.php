<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonsUpdateService.php
 * User: Tomáš Babický
 * Date: 26.03.2021
 * Time: 2:52
 */

namespace Rendix2\FamilyTree\App\Services;

use Rendix2\FamilyTree\App\Facades\RelationFacade;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Presenters\PersonPresenter;

/**
 * Class PersonsUpdateService
 *
 * @package Rendix2\FamilyTree\App\Services
 */
class PersonsUpdateService
{
    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var RelationFacade $relationFacade
     */
    private $relationFacade;

    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * PersonsUpdateService constructor.
     *
     * @param PersonSettingsManager $personSettingsManager
     * @param RelationFacade $relationFacade
     * @param WeddingFacade $weddingFacade
     */
    public function __construct(
        PersonSettingsManager $personSettingsManager,
        RelationFacade $relationFacade,
        WeddingFacade $weddingFacade
    ) {
        $this->personSettingsManager = $personSettingsManager;
        $this->relationFacade = $relationFacade;
        $this->weddingFacade = $weddingFacade;
    }

    /**
     * @param PersonPresenter $presenter
     * @param int $personId
     */
    public function prepareWeddings(PersonPresenter $presenter, $personId)
    {
        $husbands = $this->weddingFacade->getByWifeIdCached($personId);
        $wives = $this->weddingFacade->getByHusbandIdCached($personId);

        $presenter->template->wives = $wives;
        $presenter->template->husbands = $husbands;
    }

    /**
     * @param PersonPresenter $presenter
     * @param int $personId
     */
    public function prepareRelations(PersonPresenter $presenter, $personId)
    {
        $femaleRelations = $this->relationFacade->getByMaleIdCached($personId);
        $maleRelations = $this->relationFacade->getByFemaleIdCached($personId);

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
            $fathersRelations = $this->relationFacade->getByMaleIdCached($father->id);
            $mothersRelations = $this->relationFacade->getByFemaleIdCached($mother->id);
        } elseif ($father && !$mother) {
            $fathersRelations = $this->relationFacade->getByMaleIdCached($father->id);
        } elseif (!$father && $mother) {
            $mothersRelations = $this->relationFacade->getByFemaleIdCached($mother->id);
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
            $fathersWeddings = $this->weddingFacade->getByHusbandIdCached($father->id);
            $mothersWeddings = $this->weddingFacade->getByWifeIdCached($mother->id);
        } elseif ($father && !$mother) {
            $fathersWeddings = $this->weddingFacade->getByHusbandIdCached($father->id);
        } elseif (!$father && $mother) {
            $mothersWeddings = $this->weddingFacade->getByWifeIdCached($mother->id);
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
            $brothers = $this->personSettingsManager->getBrothersCached($father->id, $mother->id, $id);
            $sisters = $this->personSettingsManager->getSistersCached($father->id, $mother->id, $id);
        } elseif ($father && !$mother) {
            $brothers = $this->personSettingsManager->getBrothersCached($father->id, null, $id);
            $sisters = $this->personSettingsManager->getSistersCached($father->id, null, $id);
        } elseif (!$father && $mother) {
            $brothers = $this->personSettingsManager->getBrothersCached(null, $mother->id, $id);
            $sisters = $this->personSettingsManager->getSistersCached(null, $mother->id, $id);
        }

        $presenter->template->brothers = $brothers;
        $presenter->template->sisters = $sisters;
    }
}
