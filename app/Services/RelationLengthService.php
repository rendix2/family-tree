<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationLengthService.php
 * User: Tomáš Babický
 * Date: 31.03.2021
 * Time: 3:40
 */

namespace Rendix2\FamilyTree\App\Services;

use DateTime;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\Model\Entities\DurationEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;

/**
 * Class RelationLengthService
 *
 * @package Rendix2\FamilyTree\App\Services
 */
class RelationLengthService
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * RelationLengthService constructor.
     *
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param PersonEntity $male
     * @param PersonEntity $female
     * @param DurationEntity $durationEntity
     *
     * @return array
     */
    public function getRelationLength(
        PersonEntity $male,
        PersonEntity $female,
        DurationEntity $durationEntity
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
                $relationLength = $this->translator->translate('wedding_they_are_together', $relationLength);
            } elseif ($durationEntity->dateTo) {
                $relationLength = $durationEntity->dateTo->diff($durationEntity->dateSince);
                $relationLength = $relationLength->y;
                $relationLength = $this->translator->translate('wedding_they_were_together', $relationLength);
            }
        }

        return [
            'femaleRelationAge' => $femaleWeddingAge,
            'maleRelationAge' => $maleWeddingAge,
            'relationLength' => $relationLength
        ];
    }
}
