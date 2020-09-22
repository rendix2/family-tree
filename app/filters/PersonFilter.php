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

/**
 * Class PersonFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class PersonFilter
{

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
            $birthDate = 'NA';
        }

        $hasDeath = false;

        if ($person->hasDeathDate) {
            $hasDeath = true;

            $deathDate = $person->deathDate->format('d.m.Y');
        } elseif ($person->hasDeathYear) {
            $hasDeath = true;

            $deathDate = $person->deathYear;
        } else {
            $deathDate = 'NA';
        }

        $date = '';

        if ($hasBirth || $hasDeath) {
            $date = sprintf('(%s - %s)', $birthDate, $deathDate);
        }

        return $person->name . ' ' . $person->surname . ' ' . $date;
    }
}
