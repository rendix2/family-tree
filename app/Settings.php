<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Settings.php
 * User: Tomáš Babický
 * Date: 06.11.2020
 * Time: 21:17
 */

namespace Rendix2\FamilyTree\App;

/**
 * Class Settings
 *
 * @package Rendix2\FamilyTree\App
 */
class Settings
{
    const SETTINGS_PERSON_ORDERING = 'settings_person_order';

    const SETTINGS_PERSON_NAME_ORDER = 'settings_person_name_order';

    CONST SETTINGS_LANGUAGE = 'settings_language';

    const PERSON_ORDERING_ID = 1;

    const PERSON_ORDERING_NAME = 2;

    const PERSON_ORDERING_SURNAME = 3;

    const PERSON_ORDERING_NAME_SURNAME = 4;

    const PERSON_ORDERING_SURNAME_NAME = 5;

    const PERSON_ORDER_NAME_NAME_SURNAME = 1;

    const PERSON_ORDER_NAME_SURNAME_NAME = 2;

}
