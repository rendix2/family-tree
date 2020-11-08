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

use Dibi\Row;

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
        $husbands = $this->weddingManager->getAllByWifeId($personId);
        $wives = $this->weddingManager->getAllByHusbandId($personId);

        foreach ($husbands as $husband) {
            $husbandPerson = $this->manager->getByPrimaryKey($husband->husbandId);

            $husband->person = $husbandPerson;
        }

        foreach ($wives as $wife) {
            $wifePerson = $this->manager->getByPrimaryKey($wife->wifeId);

            $wife->person = $wifePerson;
        }

        $this->template->wives = $wives;
        $this->template->husbands = $husbands;
    }

    /**
     * @param int $personId
     */
    private function prepareRelations($personId)
    {
        $femaleRelations = $this->relationManager->getByMaleId($personId);
        $maleRelations = $this->relationManager->getByFemaleId($personId);

        foreach ($maleRelations as $relation) {
            $relationPerson = $this->manager->getByPrimaryKey($relation->maleId);

            $relation->person = $relationPerson;
        }

        foreach ($femaleRelations as $relation) {
            $relationPerson = $this->manager->getByPrimaryKey($relation->femaleId);

            $relation->person = $relationPerson;
        }

        $this->template->maleRelations = $maleRelations;
        $this->template->femaleRelations = $femaleRelations;
    }

    /**
     * @param Row|null $father
     * @param Row|null $mother
     */
    private function prepareParentsRelations($father = null, $mother = null)
    {
        $fathersRelations = [];
        $mothersRelations = [];

        if ($father && $mother) {
            $fathersRelations = $this->relationManager->getByMaleId($father->id);
            $mothersRelations = $this->relationManager->getByFemaleId($mother->id);

            foreach ($fathersRelations as $relation) {
                $relationPerson = $this->manager->getByPrimaryKey($relation->femaleId);

                $relation->person = $relationPerson;
            }

            foreach ($mothersRelations as $relation) {
                $relationPerson = $this->manager->getByPrimaryKey($relation->maleId);

                $relation->person = $relationPerson;
            }
        } elseif ($father && !$mother) {
            $fathersRelations = $this->relationManager->getByMaleId($father->id);

            foreach ($fathersRelations as $relation) {
                $relationPerson = $this->manager->getByPrimaryKey($relation->femaleId);

                $relation->person = $relationPerson;
            }
        } elseif (!$father && $mother) {
            $mothersRelations = $this->relationManager->getByFemaleId($mother->id);

            foreach ($mothersRelations as $relation) {
                $relationPerson = $this->manager->getByPrimaryKey($relation->maleId);

                $relation->person = $relationPerson;
            }
        }

        $this->template->fathersRelations = $fathersRelations;
        $this->template->mothersRelations = $mothersRelations;
    }

    /**
     * @param Row|null $father
     * @param Row|null $mother
     */
    private function prepareParentsWeddings($father = null, $mother = null)
    {
        $fathersWeddings = [];
        $mothersWeddings = [];

        if ($father && $mother) {
            $fathersWeddings = $this->weddingManager->getAllByHusbandId($father->id);
            $mothersWeddings = $this->weddingManager->getAllByWifeId($mother->id);

            foreach ($fathersWeddings as $wedding) {
                $weddingPerson = $this->manager->getByPrimaryKey($wedding->wifeId);

                $wedding->person = $weddingPerson;
            }

            foreach ($mothersWeddings as $wedding) {
                $weddingPerson = $this->manager->getByPrimaryKey($wedding->husbandId);

                $wedding->person = $weddingPerson;
            }
        } elseif ($father && !$mother) {
            $fathersWeddings = $this->weddingManager->getAllByHusbandId($father->id);

            foreach ($fathersWeddings as $wedding) {
                $weddingPerson = $this->manager->getByPrimaryKey($wedding->wifeId);

                $wedding->person = $weddingPerson;
            }
        } elseif (!$father && $mother) {
            $mothersWeddings = $this->weddingManager->getAllByWifeId($mother->id);

            foreach ($mothersWeddings as $wedding) {
                $weddingPerson = $this->manager->getByPrimaryKey($wedding->husbandId);

                $wedding->person = $weddingPerson;
            }
        }

        $this->template->fathersWeddings = $fathersWeddings;
        $this->template->mothersWeddings = $mothersWeddings;
    }

    /**
     * @param int $id
     * @param Row|null $father
     * @param Row|null $mother
     */
    private function prepareBrothersAndSisters($id, $father = null, $mother = null)
    {
        $brothers = [];
        $sisters = [];

        if ($father && $mother) {
            $brothers = $this->manager->getBrothers($father->id, $mother->id, $id);
            $sisters = $this->manager->getSisters($father->id, $mother->id, $id);
        } elseif ($father && !$mother) {
            $brothers = $this->manager->getBrothers($father->id, null, $id);
            $sisters = $this->manager->getSisters($father->id, null, $id);
        } elseif (!$father && $mother) {
            $brothers = $this->manager->getBrothers(null, $mother->id, $id);
            $sisters = $this->manager->getSisters(null, $mother->id, $id);
        }

        $this->template->brothers = $brothers;
        $this->template->sisters = $sisters;
    }
}
