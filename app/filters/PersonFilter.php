<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonFilter.php
 * User: Tomáš Babický
 * Date: 22.09.2020
 * Time: 20:38
 */

namespace Rendix2\FamilyTree\App\Filters;

use Dibi\Row;
use Nette\Http\IRequest;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\Settings;

/**
 * Class PersonFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class PersonFilter
{

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var int $orderName
     */
    private $orderName;

    /**
     * PersonFilter constructor.
     *
     * @param ITranslator $translator
     * @param IRequest $request
     */
    public function __construct(ITranslator $translator, IRequest $request)
    {
        $this->translator = $translator;
        $this->orderName = (int)$request->getCookie(Settings::SETTINGS_PERSON_NAME_ORDER);
    }

    /**
     * @param Row $person
     *
     * @return string
     */
    public function __invoke(Row $person)
    {
        $hasBirth = false;

        if ($person->hasBirthDate) {
            $hasBirth = true;

            $birthDate = $person->birthDate->format('d.m.Y');
        } elseif ($person->hasBirthYear) {
            $hasBirth = true;

            $birthDate = $person->birthYear;
        } else {
            $birthDate = $this->translator->translate('date_na');
        }

        $hasDeath = false;

        if ($person->hasDeathDate) {
            $hasDeath = true;

            $deathDate = $person->deathDate->format('d.m.Y');
        } elseif ($person->hasDeathYear) {
            $hasDeath = true;

            $deathDate = $person->deathYear;
        } else {
            if ($person->stillAlive) {
                $deathDate = $this->translator->translate('person_death_date_still_living');
            } else {
                $deathDate = $this->translator->translate('date_na');
            }
        }

        $date = '';

        if ($hasBirth || $hasDeath) {
            $date = sprintf('(%s - %s)', $birthDate, $deathDate);
        }

        if ($this->orderName === Settings::PERSON_ORDER_NAME_NAME_SURNAME) {
            return $person->name . ' ' . $person->surname . ' ' . $date;
        } elseif ($this->orderName === Settings::PERSON_ORDER_NAME_SURNAME_NAME) {
            return $person->surname . ' ' . $person->name . ' ' . $date;
        }
    }
}
