<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IPersonManager.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 3:20
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Person\Interfaces;

use Dibi\Exception;
use Dibi\Row;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Interfaces\ISelector;

interface IPersonSelector extends ISelector
{
    /**
     * @param int|null $motherId
     *
     * @return Row[]
     */
    public function getByMotherId($motherId);

    /**
     * @param int|null $motherId
     *
     * @return Row[]
     */
    public function getMalesByMotherId($motherId);

    /**
     * @param int|null $motherId
     *
     * @return Row[]
     */
    public function getFemalesByMotherId($motherId);

    /**
     * @param int|null $fatherId
     *
     * @return PersonEntity[]
     */
    public function getByFatherId($fatherId);

    /**
     * @param int|null $fatherId
     *
     * @return PersonEntity[]
     */
    public function getMalesByFatherId($fatherId);

    /**
     * @param int|null $fatherId
     *
     * @return PersonEntity[]
     */
    public function getFemalesByFatherId($fatherId);

    /**
     * @param int|null $genusId
     *
     * @return PersonEntity[]
     */
    public function getByGenusId($genusId);

    /**
     * @param int|null $townId
     *
     * @return PersonEntity[]
     */
    public function getByBirthTownId($townId);

    /**
     * @param int $addressId
     *
     * @return PersonEntity[]
     */
    public function getByBirthAddressId($addressId);

    /**
     * @param int|null $townId
     *
     * @return PersonEntity[]
     */
    public function getByDeathTownId($townId);

    /**
     * @param int $addressId
     *
     * @return PersonEntity[]
     */
    public function getByDeathAddressId($addressId);

    /**
     * @param int|null $townId
     *
     * @return PersonEntity[]
     */
    public function getByGravedTownId($townId);

    /**
     * @param int $addressId
     *
     * @return PersonEntity[]
     */
    public function getByGravedAddressId($addressId);

    /**
     * @return array
     */
    public function getAllPairs();

    /**
     *
     * @return array
     */
    public function getMalesPairs();

    /**
     * @return array
     */
    public function getFemalesPairs();

    /**
     * @param int|null $fatherId
     * @param int|null $motherId
     * @param int $personId
     *
     * @return PersonEntity[]
     */
    public function getBrothers($fatherId, $motherId, $personId);

    /**
     * @param int|null $fatherId
     * @param int|null $motherId
     * @param int $personId
     *
     * @return PersonEntity[]
     */
    public function getSisters($fatherId, $motherId, $personId);

    /**
     * @param PersonEntity $person
     *
     * @return PersonEntity[]
     * @throws Exception
     */
    public function getSonsByPerson(PersonEntity $person);

    /**
     * @param int $id
     *
     * @return Row[]
     */
    public function getSonsById($id);

    /**
     * @param PersonEntity $person
     *
     * @return Row[]
     * @throws Exception
     */
    public function getDaughtersByPerson(PersonEntity $person);

    /**
     * @param int $id
     *
     * @return Row[]
     */
    public function getDaughtersById($id);

    /**
     * @param PersonEntity $person
     *
     * @return Row[]
     * @throws Exception
     */
    public function getChildrenByPerson(PersonEntity $person);

    /**
     * @param int $id
     *
     * @return Row[]
     */
    public function getChildrenById($id);

    /**
     * @param int $id
     * @return array
     */
    public function calculateAgeById($id);

    /**
     * @param PersonEntity $person
     *
     * @return array
     */
    public function calculateAgeByPerson(PersonEntity $person);
}