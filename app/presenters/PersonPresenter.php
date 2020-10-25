<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonPresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 1:56
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Dibi\DateTime;
use Dibi\Row;
use Exception;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\DateFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonAddressFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\PersonJobFilter;
use Rendix2\FamilyTree\App\Filters\RelationFilter;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Forms\Person2AddressForm;
use Rendix2\FamilyTree\App\Forms\Person2JobForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\NoteHistoryManager;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\RelationManager;
use Rendix2\FamilyTree\App\Managers\SourceManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;

/**
 * Class PersonPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class PersonPresenter extends BasePresenter
{
    use CrudPresenter {
        actionEdit as traitActionEdit;
    }

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var PersonManager $manager
     */
    private $manager;

    /**
     * @var Person2AddressManager $person2AddressManager
     */
    private $person2AddressManager;

    /**
     * @var Person2JobManager $person2JobManager
     */
    private $person2JobManager;

    /**
     * @var NameManager $nameManager
     */
    private $nameManager;

    /**
     * @var NoteHistoryManager $noteHistoryManager
     */
    private $noteHistoryManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var RelationManager $relationManager
     */
    private $relationManager;

    /**
     * @var SourceManager $sourceManager
     */
    private $sourceManager;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * @var Row $person
     */
    private $person;

    /**
     * PersonPresenter constructor.
     *
     * @param AddressManager $addressManager
     * @param GenusManager $genusManager
     * @param JobManager $jobManager
     * @param NameManager $namesManager
     * @param NoteHistoryManager $noteHistoryManager
     * @param Person2AddressManager $person2AddressManager
     * @param Person2JobManager $person2JobManager
     * @param PersonManager $personManager
     * @param TownManager $townManager
     * @param RelationManager $relationManager
     * @param SourceManager $sourceManager
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        AddressManager $addressManager,
        GenusManager $genusManager,
        JobManager $jobManager,
        NameManager $namesManager,
        NoteHistoryManager $noteHistoryManager,
        Person2AddressManager $person2AddressManager,
        Person2JobManager $person2JobManager,
        PersonManager $personManager,
        TownManager $townManager,
        RelationManager $relationManager,
        SourceManager $sourceManager,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->addressManager = $addressManager;
        $this->genusManager = $genusManager;
        $this->jobManager = $jobManager;
        $this->manager = $personManager;
        $this->person2AddressManager = $person2AddressManager;
        $this->person2JobManager = $person2JobManager;
        $this->townManager = $townManager;
        $this->nameManager = $namesManager;
        $this->noteHistoryManager = $noteHistoryManager;
        $this->relationManager = $relationManager;
        $this->sourceManager = $sourceManager;
        $this->weddingManager = $weddingManager;
    }

    /**
     * @return void
     */
    public function beforeRender()
    {
        parent::beforeRender();

        if ($this->action !== 'default' && $this->action !== 'edit' && $this->action !== 'delete') {
            $id = $this->getParameter('id');

            $person = $this->manager->getByPrimaryKey($id);

            if (!$person) {
                $this->error('Person was not found.');
            }

            $this->template->person = $person;
        }
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $males = $this->manager->getMalesPairs($this->getTranslator());
        $females = $this->manager->getFemalesPairs($this->getTranslator());
        $genuses = $this->genusManager->getPairs('surname');
        $towns = $this->townManager->getAllPairs();

        $this['form-fatherId']->setItems($males);
        $this['form-motherId']->setItems($females);
        $this['form-genusId']->setItems($genuses);
        $this['form-birthTownId']->setItems($towns);
        $this['form-deathTownId']->setItems($towns);
        $this['form-gravedTownId']->setItems($towns);

        $this->traitActionEdit($id);
    }

    /**
     * @param int $id personId
     */
    public function actionAddress($id)
    {
        $person = $this->manager->getByPrimaryKey($id);

        if (!$person) {
            $this->error('Item not found');
        }

        $addresses = $this->addressManager->getAllPairs();

        $personFilter = new PersonFilter($this->getTranslator());

        $this['addressForm-personId']->setItems([$id => $personFilter($person)])->setDisabled()->setValue($id);
        $this['addressForm-addressId']->setItems($addresses);
    }

    /**
     * @param int $id personId
     */
    public function actionJob($id)
    {
        $person = $this->manager->getByPrimaryKey($id);

        if (!$person) {
            $this->error('Item not found');
        }

        $jobs = $this->jobManager->getAllPairs();

        $personFilter = new PersonFilter($this->getTranslator());

        $this['jobForm-personId']->setItems([$id => $personFilter($person)])->setDisabled()->setValue($id);
        $this['jobForm-jobId']->setItems($jobs);
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $persons = $this->manager->getAllFluent()->fetchAll();

        $this->template->persons = $persons;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @param int $id personId
     *
     * @throws Exception
     */
    public function renderEdit($id)
    {
        if ($id === null) {
            $father = null;
            $mother = null;

            $addresses = [];
            $names = [];

            $wives = [];
            $husbands = [];

            $maleRelations = [];
            $femaleRelations = [];

            $brothers = [];
            $sisters = [];

            $sons = [];
            $daughters = [];

            $jobs = [];

            $historyNotes = [];

            $parentsWedding = null;
            $parentsRelation = null;

            $fathersWeddings = [];
            $fathersRelations = [];

            $mothersWeddings = [];
            $mothersRelations = [];

            $age = null;
            $person = null;

            $genusPersons = [];

            $sources = [];
        } else {
            $person = $this->item;

            $addresses = $this->person2AddressManager->getFluentByLeftJoinedCountryJoinedTownJoined($id);
            $names = $this->nameManager->getByPersonId($id);
            $husbands = $this->weddingManager->getAllByWifeId($id);
            $wives = $this->weddingManager->getAllByHusbandId($id);
            $father = $this->manager->getByPrimaryKey($person->fatherId);
            $mother = $this->manager->getByPrimaryKey($person->motherId);
            $jobs = $this->person2JobManager->getAllByLeftJoined($id);
            $femaleRelations = $this->relationManager->getByMaleId($person->id);
            $maleRelations = $this->relationManager->getByFemaleId($person->id);
            $historyNotes = $this->noteHistoryManager->getByPerson($person->id);

            foreach ($maleRelations as $relation) {
                $relationPerson = $this->manager->getByPrimaryKey($relation->maleId);

                $relation->person = $relationPerson;
            }

            foreach ($femaleRelations as $relation) {
                $relationPerson = $this->manager->getByPrimaryKey($relation->femaleId);

                $relation->person = $relationPerson;
            }

            foreach ($husbands as $husband) {
                $husbandPerson = $this->manager->getByPrimaryKey($husband->husbandId);

                $husband->person = $husbandPerson;
            }

            foreach ($wives as $wife) {
                $wifePerson = $this->manager->getByPrimaryKey($wife->wifeId);

                $wife->person = $wifePerson;
            }

            $genusPersons = [];

            $brothers = [];
            $sisters = [];

            $parentsWedding = [];
            $parentsRelation = [];

            $fathersWeddings = [];
            $fathersRelations = [];

            $mothersWeddings = [];
            $mothersRelations = [];

            if ($person->genusId) {
                $genusPersons = $this->manager->getByGenusId($person->genusId);
            }

            if ($father && $mother) {
                $brothers = $this->manager->getBrothers($father->id, $mother->id, $id);
                $sisters = $this->manager->getSisters($father->id, $mother->id, $id);

                $fathersWeddings = $this->weddingManager->getAllByHusbandIdJoined($father->id);
                $fathersRelations = $this->relationManager->getByMaleIdJoined($father->id);

                $mothersWeddings = $this->weddingManager->getAllByWifeIdJoined($mother->id);
                $mothersRelations = $this->relationManager->getByFemaleIdJoined($mother->id);

                foreach ($fathersWeddings as $wedding) {
                    $weddingPerson = $this->manager->getByPrimaryKey($wedding->wifeId);

                    $wedding->person = $weddingPerson;
                }

                foreach ($fathersRelations as $relation) {
                    $relationPerson = $this->manager->getByPrimaryKey($relation->femaleId);

                    $relation->person = $relationPerson;
                }

                foreach ($mothersWeddings as $wedding) {
                    $weddingPerson = $this->manager->getByPrimaryKey($wedding->husbandId);

                    $wedding->person = $weddingPerson;
                }

                foreach ($mothersRelations as $relation) {
                    $relationPerson = $this->manager->getByPrimaryKey($relation->maleId);

                    $relation->person = $relationPerson;
                }
            } elseif ($father && !$mother) {
                $brothers = $this->manager->getBrothers($father->id, null, $id);
                $sisters = $this->manager->getSisters($father->id, null, $id);

                $fathersWeddings = $this->weddingManager->getAllByHusbandIdJoined($father->id);
                $fathersRelations = $this->relationManager->getByMaleIdJoined($father->id);

                foreach ($fathersWeddings as $wedding) {
                    $weddingPerson = $this->manager->getByPrimaryKey($wedding->wifeId);

                    $wedding->person = $weddingPerson;
                }

                foreach ($fathersRelations as $relation) {
                    $relationPerson = $this->manager->getByPrimaryKey($relation->femaleId);

                    $relation->person = $relationPerson;
                }
            } elseif (!$father && $mother) {
                $brothers = $this->manager->getBrothers(null, $mother->id, $id);
                $sisters = $this->manager->getSisters(null, $mother->id, $id);

                $mothersWeddings = $this->weddingManager->getAllByWifeIdJoined($mother->id);
                $mothersRelations = $this->relationManager->getByFemaleIdJoined($mother->id);

                foreach ($mothersWeddings as $wedding) {
                    $weddingPerson = $this->manager->getByPrimaryKey($wedding->husbandId);

                    $wedding->person = $weddingPerson;
                }

                foreach ($mothersRelations as $relation) {
                    $relationPerson = $this->manager->getByPrimaryKey($relation->maleId);

                    $relation->person = $relationPerson;
                }
            }

            $sons = $this->manager->getSonsByPerson($this->item);
            $daughters = $this->manager->getDaughtersByPerson($this->item);

            $age = $this->manager->calculateAgeByPerson($this->item);

            $sources = $this->sourceManager->getByPersonIdJoinedSourceType($id);
        }

        $this->template->addresses = $addresses;

        $this->template->names = $names;

        $this->template->wives = $wives;
        $this->template->husbands = $husbands;

        $this->template->maleRelations = $maleRelations;
        $this->template->femaleRelations = $femaleRelations;

        $this->template->father = $father;
        $this->template->mother = $mother;

        $this->template->brothers = $brothers;
        $this->template->sisters = $sisters;

        $this->template->sons = $sons;
        $this->template->daughters = $daughters;

        $this->template->fathersWeddings = $fathersWeddings;
        $this->template->fathersRelations = $fathersRelations;

        $this->template->mothersWeddings = $mothersWeddings;
        $this->template->mothersRelations = $mothersRelations;

        $this->template->jobs = $jobs;

        $this->template->historyNotes = $historyNotes;

        $this->template->age = $age;

        $this->template->person = $this->item;

        $this->template->genusPersons = $genusPersons;

        $this->template->sources = $sources;

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addFilter('job', new JobFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
        $this->template->addFilter('personJob', new PersonJobFilter($this->getTranslator()));
        $this->template->addFilter('personAddress', new PersonAddressFilter($this->getTranslator()));
        $this->template->addFilter('name', new NameFilter($this->getTranslator()));
        $this->template->addFilter('dateFT', new DateFilter($this->getTranslator()));
    }

    /**
     * @param int $id personId
     */
    public function renderAddress($id)
    {
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @param int $id personId
     */
    public function renderJob($id)
    {
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

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

        $form->addSelect('genusId', $this->getTranslator()->translate('person_genus'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_genus'));

        $form->addGroup('person_name_group');

        $form->addText('nameFonetic', 'person_name_fonetic')
            ->setNullable();

        $form->addText('callName', 'person_name_call')
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

        $form->addSelect('birthTownId', $this->getTranslator()->translate('person_birth_town'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_birth_town'));

        $form->addCheckbox('stillAlive', 'person_still_alive')
            ->addCondition(Form::EQUAL, true)
                ->toggle('age-group', false)
                ->toggle('death-group', false);

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

        $form->addSelect('deathTownId', $this->getTranslator()->translate('person_death_town'))
            ->setOption('id', 'death-town-id')
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_death_town'));

        $form->addSelect('gravedTownId', $this->getTranslator()->translate('person_graved_town'))
            ->setOption('id', 'graved-town-id')
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_graved_town'));

        $form->addGroup('person_parents_group');

        $form->addSelect('fatherId', $this->getTranslator()->translate('person_father'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_father'));

        $form->addSelect('motherId', $this->getTranslator()->translate('person_mother'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_mother'));

        $form->addGroup('person_note_group');

        $form->addTextArea('note', 'person_note', null, 15)
            ->setAttribute('class', ' form-control tinyMCE');

        $form->addSubmit('send', 'save');

        $form->onValidate[] = [$this, 'validateForm'];
        $form->onSuccess[] = [$this, 'saveForm'];
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

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveForm(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->person = $this->manager->getByPrimaryKey($id);

            if ($this->person->note !== $values->note) {
                $noteHistoryData = [
                    'personId' => $id,
                    'text' => $values->note,
                    'date' => new DateTime()
                ];

                $this->noteHistoryManager->add($noteHistoryData);
            }

            $this->manager->updateByPrimaryKey($id, $values);
            $this->flashMessage('item_updated', self::FLASH_SUCCESS);
        } else {
            $id = $this->manager->add($values);
            $this->flashMessage('item_added', self::FLASH_SUCCESS);
        }

        $this->redirect(':edit', $id);
    }

    // JOB

    /**
     * @return Form
     */
    public function createComponentJobForm()
    {
        $formFactory = new Person2JobForm($this->getTranslator());

        $form = $formFactory->create();

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];
        $form->onSuccess[] = [$this, 'saveJobForm'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveJobForm(Form $form, ArrayHash $values)
    {
        $personId = $this->getParameter('id');

        $values->personId = $personId;

        $this->person2JobManager->addGeneral((array)$values);

        $this->flashMessage('item_added', BasePresenter::FLASH_SUCCESS);
        $this->redirect(':edit', $personId);
    }

    // ADDRESS

    /**
     * @return Form
     */
    public function createComponentAddressForm()
    {
        $control = new Person2AddressForm($this->getTranslator());

        $form = $control->create();

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];
        $form->onSuccess[] = [$this, 'saveAddressForm'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveAddressForm(Form $form, ArrayHash $values)
    {
        $personId = $this->getParameter('id');

        $values->personId = $personId;

        $this->person2AddressManager->addGeneral((array)$values);

        $this->flashMessage('item_added', BasePresenter::FLASH_SUCCESS);
        $this->redirect(':edit', $personId);
    }
}
