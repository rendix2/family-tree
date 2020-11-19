<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonPrepareMethods.php
 * User: Tomáš Babický
 * Date: 27.10.2020
 * Time: 2:27
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;

/**
 * Trait PersonPrepareMethods
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonPrepareMethods
{
    /**
     * @param int $personId
     */
    private function prepareWeddings($personId)
    {
        $husbands = $this->weddingFacade->getByWifeCached($personId);
        $wives = $this->weddingFacade->getByHusbandCached($personId);

        $this->template->wives = $wives;
        $this->template->husbands = $husbands;
    }

    /**
     * @param int $personId
     */
    private function prepareRelations($personId)
    {
        $femaleRelations = $this->relationFacade->getByMaleIdCached($personId);
        $maleRelations = $this->relationFacade->getByFemaleIdCached($personId);

        $this->template->maleRelations = $maleRelations;
        $this->template->femaleRelations = $femaleRelations;
    }

    /**
     * @param PersonEntity|null $father
     * @param PersonEntity|null $mother
     */
    private function prepareParentsRelations($father = null, $mother = null)
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

        $this->template->fathersRelations = $fathersRelations;
        $this->template->mothersRelations = $mothersRelations;
    }

    /**
     * @param PersonEntity|null $father
     * @param PersonEntity|null $mother
     */
    private function prepareParentsWeddings($father = null, $mother = null)
    {
        $fathersWeddings = [];
        $mothersWeddings = [];

        if ($father && $mother) {
            $fathersWeddings = $this->weddingFacade->getByHusbandCached($father->id);
            $mothersWeddings = $this->weddingFacade->getByWifeCached($mother->id);
        } elseif ($father && !$mother) {
            $fathersWeddings = $this->weddingFacade->getByHusbandCached($father->id);
        } elseif (!$father && $mother) {
            $mothersWeddings = $this->weddingFacade->getByWifeCached($mother->id);
        }

        $this->template->fathersWeddings = $fathersWeddings;
        $this->template->mothersWeddings = $mothersWeddings;
    }

    /**
     * @param int $id
     * @param PersonEntity|null $father
     * @param PersonEntity|null $mother
     */
    private function prepareBrothersAndSisters($id, $father = null, $mother = null)
    {
        $brothers = [];
        $sisters = [];

        if ($father && $mother) {
            $brothers = $this->manager->getBrothersCached($father->id, $mother->id, $id);
            $sisters = $this->manager->getSistersCached($father->id, $mother->id, $id);
        } elseif ($father && !$mother) {
            $brothers = $this->manager->getBrothersCached($father->id, null, $id);
            $sisters = $this->manager->getSistersCached($father->id, null, $id);
        } elseif (!$father && $mother) {
            $brothers = $this->manager->getBrothersCached(null, $mother->id, $id);
            $sisters = $this->manager->getSistersCached(null, $mother->id, $id);
        }

        $this->template->brothers = $brothers;
        $this->template->sisters = $sisters;
    }
}
