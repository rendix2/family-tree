<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonForm.php
 * User: Tomáš Babický
 * Date: 19.11.2020
 * Time: 21:30
 */

namespace Rendix2\FamilyTree\App\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Forms\Settings\PersonSettings;

/**
 * Class PersonForm
 *
 * @package Rendix2\FamilyTree\App\Forms
 */
class PersonForm
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PersonSettings $personSettings
     */
    private $personSettings;

    /**
     * PersonForm constructor.
     *
     * @param ITranslator $translator
     * @param PersonSettings $personSettings
     */
    public function __construct(
        ITranslator $translator,
        PersonSettings $personSettings
    ) {
        $this->translator = $translator;
        $this->personSettings = $personSettings;
    }

    /**
     * @return Form
     */
    public function create()
    {
        $form = new Form();

        $form->setTranslator($this->translator);

        $form->addProtection();

        $form->addGroup('person_personal_data_group');

        $form->addText('name', 'person_name')
            ->setRequired('person_name_required');

        $form->addText('surname', 'person_surname')
            ->setRequired('person_surname_required');

        $form->addRadioList('gender', 'person_gender', ['m' => 'person_male', 'f' => 'person_female'])
            ->setRequired('person_gender_required');

        $form->addCheckbox('hasAge', 'person_has_age')
            ->addCondition(Form::EQUAL, true)
            ->toggle('age');

        $form->addInteger('age', 'person_age')
            ->setNullable()
            ->setOption('id', 'age')
            ->addRule($form::RANGE, 'person_age_range_error', [0, 130])
            ->addConditionOn($form['hasAge'], Form::EQUAL, true)
            ->setRequired('person_age_is_required')
            ->endCondition()
            ->addCondition(Form::FILLED)
            ->addConditionOn($form['hasAge'], Form::EQUAL, false)
            ->setRequired('person_has_age_is_required')
            ->addRule(Form::EQUAL, 'person_has_age_is_required', true)
            ->endCondition();

        $form->addSelect('genusId', $this->translator->translate('person_genus'))
            ->setTranslator()
            ->setPrompt($this->translator->translate('person_select_genus'));

        $form->addGroup('person_name_group');

        $form->addText('nameFonetic', 'person_name_fonetic')
            ->setNullable();

        $form->addText('nameCall', 'person_name_call')
            ->setNullable();

        $form->addGroup('person_birth_group');

        // birth date

        $form->addCheckbox('hasBirthDate', 'person_has_birth_date')
            ->setOption('id', 'has-birth-date')
            ->addCondition(Form::EQUAL, true)
            ->toggle('birth-date')
            ->toggle('has-birth-year', false)
            ->endCondition();

        $form->addTbDatePicker('birthDate', 'person_birth_date')
            ->setNullable()
            ->setOption('id', 'birth-date')
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date')
            ->addConditionOn($form['hasBirthDate'], Form::EQUAL, true)
            ->setRequired('person_birth_date_is_required')
            ->endCondition()
            ->addCondition(Form::FILLED)
            ->addConditionOn($form['hasBirthDate'], Form::EQUAL, false)
            ->setRequired('person_has_birth_date_is_required')
            ->addRule(Form::EQUAL, 'person_has_birth_date_is_required', true)
            ->endCondition();

        // birth date

        // birth year

        $form->addCheckbox('hasBirthYear', 'person_has_birth_year')
            ->setOption('id', 'has-birth-year')
            ->addCondition(Form::EQUAL, true)
            ->toggle('birth-year')
            ->toggle('has-birth-date', false);

        $form->addInteger('birthYear', 'person_birth_year')
            ->setNullable()
            ->setOption('id', 'birth-year')
            ->addConditionOn($form['hasBirthYear'], Form::EQUAL, true)
            ->setRequired('person_birth_year_is_required')
            ->endCondition()
            ->addCondition(Form::FILLED)
            ->addConditionOn($form['hasBirthYear'], Form::EQUAL, false)
            ->setRequired('person_has_birth_year_is_required')
            ->addRule(Form::EQUAL, 'person_has_birth_year_is_required', true)
            ->endCondition();

        // birth year

        $form->addSelect('birthTownId', $this->translator->translate('person_birth_town'))
            ->setAttribute('data-link', $this->personSettings->selectBirthTownHandle)
            ->setTranslator()
            ->setPrompt($this->translator->translate('person_select_birth_town'));

        $form->addSelect('birthAddressId', $this->translator->translate('person_birth_address'))
            ->setTranslator()
            ->setPrompt($this->translator->translate('person_select_birth_address'));

        $form->addCheckbox('stillAlive', 'person_still_alive')
            ->addCondition(Form::EQUAL, true)
            ->toggle('age-group', false)
            ->toggle('death-group', false)
            ->toggle('graved-group', false);

        $form->addGroup('person_death_group')->setOption('id', 'death-group');

        // death date

        $form->addCheckbox('hasDeathDate', 'person_has_death_date')
            ->setOption('id', 'has-death-date')
            ->addCondition(Form::EQUAL, true)
            ->toggle('death-date')
            ->toggle('has-death-year', false)
            ->endCondition();

        $form->addTbDatePicker('deathDate', 'person_dead_date')
            ->setNullable()
            ->setOption('id', 'death-date')
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date')
            ->addConditionOn($form['hasDeathDate'], Form::EQUAL, true)
            ->setRequired('person_death_date_is_required')
            ->endCondition()
            ->addCondition(Form::FILLED)
            ->addConditionOn($form['hasDeathDate'], Form::EQUAL, false)
            ->setRequired('person_has_death_date_is_required')
            ->addRule(Form::EQUAL, 'person_has_death_date_is_required', true)
            ->endCondition();

        // death date

        // death year

        $form->addCheckbox('hasDeathYear', 'person_has_death_year')
            ->setOption('id', 'has-death-year')
            ->addCondition(Form::EQUAL, true)
            ->toggle('death-year')
            ->toggle('has-death-date', false)
            ->endCondition();

        $form->addInteger('deathYear', 'person_death_year')
            ->setNullable()
            ->setOption('id', 'death-year')
            ->addConditionOn($form['hasDeathYear'], Form::EQUAL, true)
            ->setRequired('person_death_year_is_required')
            ->endCondition()
            ->addCondition(Form::FILLED)
            ->addConditionOn($form['hasDeathYear'], Form::EQUAL, false)
            ->setRequired('person_has_death_year_is_required')
            ->addRule(Form::EQUAL, 'person_has_death_year_is_required', true)
            ->endCondition();

        // death year

        $form->addSelect('deathTownId', $this->translator->translate('person_death_town'))
            ->setAttribute('data-link', $this->personSettings->selectDeathTownHandle)
            ->setOption('id', 'death-town-id')
            ->setTranslator()
            ->setPrompt($this->translator->translate('person_select_death_town'));

        $form->addSelect('deathAddressId', $this->translator->translate('person_death_address'))
            ->setTranslator()
            ->setPrompt($this->translator->translate('person_select_death_address'));

        $form->addGroup('person_graved_group')
            ->setOption('id', 'graved-group');

        $form->addSelect('gravedTownId', $this->translator->translate('person_graved_town'))
            ->setAttribute('data-link', $this->personSettings->selectGravedTownHandle)
            ->setOption('id', 'graved-town-id')
            ->setTranslator()
            ->setPrompt($this->translator->translate('person_select_graved_town'));

        $form->addSelect('gravedAddressId', $this->translator->translate('person_graved_address'))
            ->setTranslator()
            ->setPrompt($this->translator->translate('person_select_graved_address'));

        $form->addGroup('person_parents_group');

        $form->addSelect('fatherId', $this->translator->translate('person_father'))
            ->setTranslator()
            ->setPrompt($this->translator->translate('person_select_father'));

        $form->addSelect('motherId', $this->translator->translate('person_mother'))
            ->setTranslator()
            ->setPrompt($this->translator->translate('person_select_mother'));

        $form->addGroup('person_note_group');

        $form->addTextArea('note', 'person_note', null, 15)
            ->setAttribute('class', ' form-control tinyMCE');

        $form->addSubmit('send', 'person_save_person');

        $form->onValidate[] = [$this, 'validateForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function validateForm(Form $form, ArrayHash $values)
    {
        if ($values->birthYear && $values->birthDate) {
            $form->addError('person_has_birth_year_and_birth_date');
        }

        if ($values->deathYear && $values->deathDate) {
            $form->addError('person_has_death_year_and_death_date');
        }

        if ($values->stillAlive) {
            if ($values->hasDeathDate) {
                $form->addError('person_still_alive_is_checked_and_has_death_date');
            }

            if ($values->deathDate) {
                $form->addError('person_still_alive_is_checked_and_death_date');
            }

            if ($values->hasDeathYear) {
                $form->addError('person_still_alive_is_checked_and_has_death_year');
            }

            if ($values->deathYear) {
                $form->addError('person_still_alive_is_checked_and_death_year');
            }

            if ($values->deathTownId) {
                $form->addError('person_still_alive_is_checked_and_death_town');
            }

            if ($values->gravedTownId ) {
                $form->addError('person_still_alive_is_checked_and_graved_town');
            }
        }

        if ($values->hasAge && $values->age) {
            if ($values->birthDate) {
                $form->addError('person_has_age_and_birth_date');
            }

            if ($values->birthYear) {
                $form->addError('person_has_age_and_birth_year');
            }

            if ($values->deathDate) {
                $form->addError('person_has_age_and_death_date');
            }

            if ($values->deathYear) {
                $form->addError('person_has_age_and_death_year');
            }
        }

        if ($values->hasAge && $values->age && $values->stillAlive) {
            $form->addError('person_has_age_and_still_alive');
        }
    }
}
