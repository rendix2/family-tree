<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAgeService.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 23:18
 */

namespace Rendix2\FamilyTree\App\Services;


use Dibi\DateTime;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;

class PersonAgeService
{

    /**
     * @param PersonEntity $person
     *
     * @return array
     */
    public function calculateAgeByPerson(PersonEntity $person)
    {
        $age = null;
        $nowAge = null;
        $yearsAfterDeath= null;
        $accuracy = null;
        $now = new DateTime();
        $nowYear = $now->format('Y');

        if ($person->hasBirthDate && $person->hasDeathDate) {
            $deathYear = (int)$person->deathDate->format('Y');
            $birthYear = (int)$person->birthDate->format('Y');

            if ($birthYear > 1970 && $deathYear > 1970) {
                $diff = $person->deathDate->diff($person->birthDate);
                $nowDiff = $now->diff($person->birthDate);

                $age = $diff->y;
                $nowAge = $nowDiff->y;
                $yearsAfterDeath = $nowAge - $age;
                $accuracy = 1;
            } else {
                $age = $deathYear - $birthYear;
                $nowAge = $nowYear - $birthYear;
                $yearsAfterDeath = $nowAge - $age;
                $accuracy = 3;
            }
        } elseif ($person->hasDeathYear && $person->hasBirthYear) {
            $age = $person->deathYear - $person->birthYear;
            $nowAge = $nowYear - $person->birthYear;
            $yearsAfterDeath = $nowAge - $age;
            $accuracy = 2;
        } elseif ($person->hasDeathDate && $person->hasBirthYear) {
            $deathYear = (int)$person->deathDate->format('Y');

            if ($deathYear > 1970) {
                if ($person->birthYear > 1970) {
                    $birthYearDateTime = new DateTime($person->birthYear);
                    $diff = $person->deathDate->diff($birthYearDateTime);
                    $nowDiff = $now->diff($birthYearDateTime);

                    $age = $diff->y;
                    $nowAge = $nowDiff->y;
                    $yearsAfterDeath = $nowAge - $age;
                    $accuracy = 2;
                } else {
                    $age = $deathYear - $person->birthYear;
                    $nowAge = $nowYear - $person->birthYear;
                    $yearsAfterDeath = $nowAge - $age;
                    $accuracy = 3 ;
                }
            } else {
                $age = $deathYear - $person->birthYear;
                $nowAge = $nowYear - $person->birthYear;
                $yearsAfterDeath = $nowAge - $age;
                $accuracy = 3 ;
            }
        } elseif ($person->hasDeathYear && $person->hasBirthDate) {
            $birthDate = (int)$person->birthDate->format('Y');

            if ($birthDate > 1970) {
                if ($person->deathYear > 1970) {
                    $deathYearDateTime = new DateTime($person->deathYear);

                    $diff = $deathYearDateTime->diff($person->birthDate);
                    $nowDiff = $now->diff($person->birthDate);

                    $age = $diff->y;
                    $nowAge = $nowDiff->y;
                    $yearsAfterDeath = $nowAge - $age;
                    $accuracy = 2;
                } else {
                    $age = $person->deathYear - $person->birthYear;
                    $nowAge = $nowYear - $person->birthYear;
                    $yearsAfterDeath = $nowAge - $age;
                    $accuracy = 3 ;
                }
            } else {
                $age = $person->deathYear - $birthDate;
                $nowAge = $nowYear - $birthDate;
                $yearsAfterDeath = $nowAge - $age;
                $accuracy = 3 ;
            }
        } elseif ($person->stillAlive) {
            if ($person->hasBirthDate) {
                $birthYear = $person->birthDate->format('Y');

                if ($birthYear > 1970) {
                    $diff = $now->diff($person->birthDate);

                    $age = $diff->y;
                    $accuracy = 1;
                } else {
                    $now = new DateTime();
                    $nowYear = $now->format('Y');

                    $age = $nowYear - $birthYear;
                    $accuracy = 2;
                }
            } elseif($person->hasBirthYear) {
                if ($person->hasBirthYear > 1970) {
                    $birthYearDateTime = new DateTime($person->birthYear);

                    $diff = $now->diff($birthYearDateTime);

                    $age = $diff->y;
                    $accuracy = 1;
                } else {
                    $age = $nowYear - $person->birthYear;
                    $accuracy = 2;
                }
            }
        } elseif ($person->hasAge) {
            $age = $person->age;

            $accuracy = 1;
        }

        return [
            'age' => $age,
            'accuracy' => $accuracy,
            'nowAge' => $nowAge,
            'yearsAfterDeath' => $yearsAfterDeath
        ];
    }

}