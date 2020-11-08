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
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\NoteHistoryManager;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\RelationManager;
use Rendix2\FamilyTree\App\Managers\SourceManager;
use Rendix2\FamilyTree\App\Managers\SourceTypeManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddBrotherModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddDaughterModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddress;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddSisterModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddSonModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteAddressModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteBrotherModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteDaughterModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteGenusModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteHistoryNoteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteJobModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteNameModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteRelationModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteRelationParentModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteSisterModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteSonModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteSourceModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteWeddingModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteWeddingParentModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonJob;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonPrepareMethods;

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

    use PersonDeleteGenusModal;

    use PersonAddBrotherModal;
    use PersonDeleteBrotherModal;

    use PersonAddSisterModal;
    use PersonDeleteSisterModal;

    use PersonAddSonModal;
    use PersonDeleteSonModal;

    use PersonAddDaughterModal;
    use PersonDeleteDaughterModal;

    use PersonPrepareMethods;

    use PersonAddress;
    use PersonJob;

    use PersonDeleteNameModal;
    use PersonDeleteAddressModal;
    use PersonDeleteJobModal;

    use PersonDeleteWeddingModal;
    use PersonDeleteWeddingParentModal;

    use PersonDeleteRelationModal;
    use PersonDeleteRelationParentModal;

    use PersonDeleteSourceModal;

    use PersonDeleteHistoryNoteModal;

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
     * @var NoteHistoryManager $historyNoteManager
     */
    private $historyNoteManager;

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
     * @var SourceTypeManager $sourceTypeManager
     */
    private $sourceTypeManager;

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
     * @param SourceTypeManager $sourceTypeManager
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
        SourceTypeManager $sourceTypeManager,
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
        $this->historyNoteManager = $noteHistoryManager;
        $this->relationManager = $relationManager;
        $this->sourceManager = $sourceManager;
        $this->sourceTypeManager = $sourceTypeManager;
        $this->weddingManager = $weddingManager;
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
        $addresses = $this->addressManager->getAllPairs();

        $this['form-fatherId']->setItems($males);
        $this['form-motherId']->setItems($females);
        $this['form-genusId']->setItems($genuses);

        // towns
        $this['form-birthTownId']->setItems($towns);
        $this['form-deathTownId']->setItems($towns);
        $this['form-gravedTownId']->setItems($towns);

        // addresses
        $this['form-birthAddressId']->setItems($addresses);
        $this['form-deathAddressId']->setItems($addresses);
        $this['form-gravedAddressId']->setItems($addresses);

        $this->traitActionEdit($id);
    }

    /**
     * @param int|null $id personId
     *
     * @throws Exception
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $father = null;
            $mother = null;

            $addresses = [];
            $names = [];

            $sons = [];
            $daughters = [];

            $jobs = [];

            $historyNotes = [];

            $age = null;
            $person = null;

            $genusPersons = [];

            $sources = [];
        } else {
            $person = $this->item;

            $father = $this->manager->getByPrimaryKey($person->fatherId);
            $mother = $this->manager->getByPrimaryKey($person->motherId);

            $addresses = $this->person2AddressManager->getAllByLeftJoinedCountryJoinedTownJoined($id);

            $names = $this->nameManager->getByPersonId($id);

            $jobs = $this->person2JobManager->getAllByLeftJoined($id);

            $historyNotes = $this->historyNoteManager->getByPerson($person->id);

            $genusPersons = [];

            if (!$this->isAjax() && $person->genusId) {
                $genusPersons = $this->manager->getByGenusId($person->genusId);
            }

            $sons = $this->manager->getSonsByPerson($this->item);
            $daughters = $this->manager->getDaughtersByPerson($this->item);

            $age = $this->manager->calculateAgeByPerson($this->item);

            $sources = $this->sourceManager->getByPersonId($id);

            foreach ($sources as $source) {
                $sourceType = $this->sourceTypeManager->getByPrimaryKey($source->sourceTypeId);

                $source->sourceType = $sourceType;
            }
        }

        $this->template->addresses = $addresses;

        $this->template->names = $names;

        $this->template->father = $father;
        $this->template->mother = $mother;

        $this->template->sons = $sons;
        $this->template->daughters = $daughters;

        $this->template->jobs = $jobs;

        $this->template->historyNotes = $historyNotes;

        $this->template->age = $age;

        $this->template->person = $this->item;

        $this->template->genusPersons = $genusPersons;

        $this->template->sources = $sources;

        $this->prepareWeddings($id);
        $this->prepareRelations($id);

        $this->prepareParentsRelations($father, $mother);
        $this->prepareParentsWeddings($father, $mother);

        $this->prepareBrothersAndSisters($id, $father, $mother);

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addFilter('job', new JobFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        $this->template->addFilter('name', new NameFilter());
        $this->template->addFilter('dateFT', new DateFilter($this->getTranslator()));
    }

    /**
     * @param $id
     */
    public function actionView($id)
    {
        $this->item = $item = $this->manager->getByPrimaryKey($id);

        if (!$item) {
            $this->error('Item not found.');
        }

        $males = $this->manager->getMalesPairs($this->getTranslator());
        $females = $this->manager->getFemalesPairs($this->getTranslator());
        $genuses = $this->genusManager->getPairs('surname');
        $towns = $this->townManager->getAllPairs();
        $addresses = $this->addressManager->getAllPairs();

        $this['form-fatherId']->setItems($males);
        $this['form-motherId']->setItems($females);
        $this['form-genusId']->setItems($genuses);

        // towns
        $this['form-birthTownId']->setItems($towns);
        $this['form-deathTownId']->setItems($towns);
        $this['form-gravedTownId']->setItems($towns);

        // addresses
        $this['form-birthAddressId']->setItems($addresses);
        $this['form-deathAddressId']->setItems($addresses);
        $this['form-gravedAddressId']->setItems($addresses);

        foreach ($this['form']->getComponents() as $component) {
            $component->setDisabled();
        }

        $this['form']->setDefaults($item);
    }

    /**
     * @param int $id personId
     *
     * @throws Exception
     */
    public function renderView($id)
    {
        $person = $this->item;

        $father = $this->manager->getByPrimaryKey($person->fatherId);
        $mother = $this->manager->getByPrimaryKey($person->motherId);

        $addresses = $this->person2AddressManager->getAllByLeftJoinedCountryJoinedTownJoined($id);

        $names = $this->nameManager->getByPersonId($id);

        $jobs = $this->person2JobManager->getAllByLeftJoined($id);

        $historyNotes = $this->historyNoteManager->getByPerson($person->id);

        $genusPersons = [];

        if ($person->genusId) {
            $genusPersons = $this->manager->getByGenusId($person->genusId);
        }

        $sons = $this->manager->getSonsByPerson($this->item);
        $daughters = $this->manager->getDaughtersByPerson($this->item);

        $age = $this->manager->calculateAgeByPerson($this->item);

        $sources = $this->sourceManager->getByPersonId($id);

        foreach ($sources as $source) {
            $sourceType = $this->sourceTypeManager->getByPrimaryKey($source->sourceTypeId);

            $source->sourceType = $sourceType;
        }

        $this->template->addresses = $addresses;

        $this->template->names = $names;

        $this->template->father = $father;
        $this->template->mother = $mother;

        $this->template->sons = $sons;
        $this->template->daughters = $daughters;

        $this->template->jobs = $jobs;

        $this->template->historyNotes = $historyNotes;

        $this->template->age = $age;

        $this->template->person = $this->item;

        $this->template->genusPersons = $genusPersons;

        $this->template->sources = $sources;

        $this->prepareWeddings($id);
        $this->prepareRelations($id);

        $this->prepareParentsRelations($father, $mother);
        $this->prepareParentsWeddings($father, $mother);

        $this->prepareBrothersAndSisters($id, $father, $mother);

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addFilter('job', new JobFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        $this->template->addFilter('name', new NameFilter());
        $this->template->addFilter('dateFT', new DateFilter($this->getTranslator()));
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $persons = $this->manager->getAllFluent()->fetchAll();

        $this->template->persons = $persons;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
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

        $form->addSelect('birthAddressId', $this->getTranslator()->translate('person_birth_address'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_birth_address'));

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

        $form->addSelect('deathAddressId', $this->getTranslator()->translate('person_death_address'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_death_address'));

        $form->addSelect('gravedTownId', $this->getTranslator()->translate('person_graved_town'))
            ->setOption('id', 'graved-town-id')
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_graved_town'));

        $form->addSelect('gravedAddressId', $this->getTranslator()->translate('person_graved_address'))->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_graved_address'));

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

        $form->addSubmit('send', 'person_save_person');

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

                $this->historyNoteManager->add($noteHistoryData);
            }

            $this->manager->updateByPrimaryKey($id, $values);
            $this->flashMessage('item_updated', self::FLASH_SUCCESS);
        } else {
            $id = $this->manager->add($values);
            $this->flashMessage('item_added', self::FLASH_SUCCESS);
        }

        $this->redirect(':edit', $id);
    }
}
