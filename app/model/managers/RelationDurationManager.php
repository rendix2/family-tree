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
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\Model\Entities\DurationEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;

/**
 * Trait DurationManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
trait RelationDurationManager
{
    /**
     * @param PersonEntity $male
     * @param PersonEntity $female
     * @param DurationEntity $durationEntity
     * @param ITranslator $translator
     *
     * @return array
     */
    public function getRelationLength(
        PersonEntity $male,
        PersonEntity $female,
        DurationEntity $durationEntity,
        ITranslator $translator
    ) {
        $femaleWeddingAge = null;
        $maleWeddingAge = null;
        $relationLength = null;

        if ($durationEntity->dateSince) {
            if ($female->hasBirthDate) {
                $femaleWeddingAge = $durationEntity->dateSince->diff($female->birthDate);
                $femaleWeddingAge = $femaleWeddingAge->y;
            } elseif ($female->hasBirthYear) {
                $birthDate = new DateTime($female->birthYear);

                $femaleWeddingAge = $durationEntity->dateSince->diff($birthDate);
                $femaleWeddingAge = $femaleWeddingAge->y;
            }

            if ($male->hasBirthDate) {
                $maleWeddingAge = $durationEntity->dateSince->diff($male->birthDate);
                $maleWeddingAge = $maleWeddingAge->y;
            } elseif ($male->hasBirthYear) {
                $birthDate = new DateTime($male->birthYear);

                $maleWeddingAge = $durationEntity->dateSince->diff($birthDate);
                $maleWeddingAge = $maleWeddingAge->y;
            }

            if ($durationEntity->untilNow) {
                $now = new DateTime();

                $relationLength = $now->diff($durationEntity->dateSince);
                $relationLength = $relationLength->y;
                $relationLength = $translator->translate('wedding_they_are_together', $relationLength);
            } elseif ($durationEntity->dateTo) {
                $relationLength = $durationEntity->dateTo->diff($durationEntity->dateSince);
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
