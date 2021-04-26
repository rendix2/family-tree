<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonTable.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 3:30
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Person;

use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Managers\Tables;

/**
 * Class PersonTable
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Person
 */
class PersonTable implements ITable
{
    public function getTableName()
    {
        return Tables::PERSON_TABLE;
    }

    public function getEntity()
    {
        return PersonEntity::class;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getColumns()
    {
        return [
          'id',
            'gender',
            'name',
            'nameFonetic',
            'nameCall',
            'surname',
            'hasBirthDate',
            'birthDate',
            'hasBirthYear',
            'birthYear',
            'stillAlive',
            'hasDeathDate',
            'deathDate',
            'hasDeathYear',
            'deathYear',
            'hasAge',
            'age',
            'motherId',
            'fatherId',
            'genusId',
            'birthTownId',
            'birthAddressId',

            'deathTownId',
            'deathAddressId',

            'gravedTownId',
            'gravedAddressId',

            'note'

        ];
    }
}
