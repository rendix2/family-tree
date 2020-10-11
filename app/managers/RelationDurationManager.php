<?php
/**
 *
 * Created by PhpStorm.
 * Filename: DurationManager.php
 * User: Tomáš Babický
 * Date: 12.10.2020
 * Time: 0:45
 */

namespace Rendix2\FamilyTree\App\Managers;

use DateTime;
use Dibi\Row;
use Nette\Localization\ITranslator;

/**
 * Trait DurationManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
trait RelationDurationManager
{
    /**
     * @param Row $male
     * @param Row $female
     * @param Row $relation
     * @return array
     */
    public function calcLengthRelation(Row $male,Row $female,Row $relation, ITranslator $translator)
    {
        $femaleWeddingAge = null;
        $maleWeddingAge = null;
        $relationLength = null;

        if ($relation->dateSince) {
            if ($female->hasBirthDate) {
                $femaleWeddingAge = $relation->dateSince->diff($female->birthDate);
                $femaleWeddingAge = $femaleWeddingAge->y;
            } elseif ($female->hasBirthYear) {
                $birthDate = new DateTime($female->birthYear);

                $femaleWeddingAge = $relation->dateSince->diff($birthDate);
                $femaleWeddingAge = $femaleWeddingAge->y;
            }

            if ($male->hasBirthDate) {
                $maleWeddingAge = $relation->dateSince->diff($male->birthDate);
                $maleWeddingAge = $maleWeddingAge->y;
            } elseif ($male->hasBirthYear) {
                $birthDate = new DateTime($male->birthYear);

                $maleWeddingAge = $relation->dateSince->diff($birthDate);
                $maleWeddingAge = $maleWeddingAge->y;
            }

            if ($relation->untilNow) {
                $now = new DateTime();

                $relationLength = $now->diff($relation->dateSince);
                $relationLength = $relationLength->y;
                $relationLength = $translator->translate('wedding_they_are_together', $relationLength);
            } elseif ($relation->dateTo) {
                $relationLength = $relation->dateTo->diff($relation->dateSince);
                $relationLength = $relationLength->y;
                $relationLength = $translator->translate('wedding_they_were_together', $relationLength);
            }
        }

        return [
            'femaleRelationAge' => $femaleWeddingAge,
            'maleRelationAge' => $maleWeddingAge,
            'relationLength' => $relationLength
        ];
    }
}
