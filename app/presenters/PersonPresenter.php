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
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\RelationFilter;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Forms\PersonAddressForm;
use Rendix2\FamilyTree\App\Forms\PersonFemaleRelationsForm;
use Rendix2\FamilyTree\App\Forms\PersonHusbandsForm;
use Rendix2\FamilyTree\App\Forms\PersonJobForm;
use Rendix2\FamilyTree\App\Forms\PersonMaleRelationsForm;
use Rendix2\FamilyTree\App\Forms\PersonWivesForm;
use Rendix2\FamilyTree\App\Forms\PersonNamesForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\NoteHistoryManager;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PlaceManager;
use Rendix2\FamilyTree\App\Managers\RelationManager;
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
     * @var PersonManager $manager
     */
    private $manager;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var Person2AddressManager $person2AddressManager
     */
    private $person2AddressManager;

    /**
     * @var NameManager $namesManager
     */
    private $namesManager;

    /**
     * @var NoteHistoryManager $noteHistoryManager
     */
    private $noteHistoryManager;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var Person2JobManager $person2JobManager
     */
    private $person2JobManager;

    /**
     * @var PlaceManager $placeManager
     */
    private $placeManager;

    /**
     * @var RelationManager $relationManager
     */
    private $relationManager;

    /**
     * @var Row $person
     */
    private $person;

    /**
     * PersonPresenter constructor.
     *
     * @param AddressManager $addressManager
     * @param JobManager $jobManager
     * @param GenusManager $genusManager
     * @param PersonManager $manager
     * @param Person2AddressManager $person2AddressManager
     * @param Person2JobManager $person2JobManager
     * @param PlaceManager $placeManager
     * @param RelationManager $relationManager
     * @param NameManager $namesManager
     * @param NoteHistoryManager $noteHistoryManager
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        AddressManager $addressManager,
        JobManager $jobManager,
        GenusManager $genusManager,
        PersonManager $manager,
        Person2AddressManager $person2AddressManager,
        Person2JobManager $person2JobManager,
        PlaceManager $placeManager,
        RelationManager $relationManager,
        NameManager $namesManager,
        NoteHistoryManager $noteHistoryManager,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->manager = $manager;

        $this->addressManager = $addressManager;
        $this->jobManager = $jobManager;
        $this->genusManager = $genusManager;
        $this->person2AddressManager = $person2AddressManager;
        $this->person2JobManager = $person2JobManager;
        $this->placeManager = $placeManager;
        $this->relationManager = $relationManager;
        $this->namesManager = $namesManager;
        $this->noteHistoryManager = $noteHistoryManager;
        $this->weddingManager = $weddingManager;
    }

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
     * @return void
     */
    public function renderDefault()
    {
        $persons = $this->manager->getAllFluent()->fetchAll();

        $this->template->persons = $persons;
    }

    /**
     * @param int $id
     */
    public function actionDelete($id)
    {
        $this->manager->deleteByPrimaryKey($id);
        $this->flashMessage('item_deleted', self::FLASH_SUCCESS);
        $this->redirect(':default');
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $males = $this->manager->getMalesPairs();
        $females = $this->manager->getFemalesPairs();
        $genuses = $this->genusManager->getPairs('surname');
        $places = $this->placeManager->getPairs('name');

        $this['form-fatherId']->setItems($males);
        $this['form-motherId']->setItems($females);
        $this['form-genusId']->setItems($genuses);
        $this['form-birthPlaceId']->setItems($places);
        $this['form-deathPlaceId']->setItems($places);
        $this['form-gravedPlaceId']->setItems($places);

        $this->traitActionEdit($id);
    }

    /**
     * @param int $id
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

            $children = [];
            $jobs = [];

            $historyNotes = [];

            $age = null;
        } else {
            $person = $this->manager->getByPrimaryKey($id);

            $addresses = $this->person2AddressManager->getFluentByLeftJoined($id)->orderBy('dateSince', \dibi::ASC);
            $names = $this->namesManager->getByPersonId($id);
            $husbands = $this->weddingManager->getAllByWifeIdJoined($id);
            $wives = $this->weddingManager->getAllByHusbandIdJoined($id);
            $father = $this->manager->getByPrimaryKey($person->fatherId);
            $mother = $this->manager->getByPrimaryKey($person->motherId);
            $jobs = $this->person2JobManager->getAllByLeftJoined($id);
            $femaleRelations = $this->relationManager->getByMaleIdJoined($person->id);
            $maleRelations = $this->relationManager->getByFemaleIdJoined($person->id);
            $historyNotes = $this->noteHistoryManager->getByPerson($person->id);

            if ($father && $mother) {
                $brothers = $this->manager->getBrothers($father->id, $mother->id, $id);
                $sisters = $this->manager->getSisters($father->id, $mother->id, $id);
            } elseif ($father && !$mother) {
                $brothers = $this->manager->getBrothers($father->id, null, $id);
                $sisters = $this->manager->getSisters($father->id, null, $id);
            } elseif (!$father && $mother) {
                $brothers = $this->manager->getBrothers(null, $mother->id, $id);
                $sisters = $this->manager->getSisters(null, $mother->id, $id);
            } else {
                $brothers = [];
                $sisters = [];
            }

            $children = $this->manager->getChildrenByPerson($person);

            $age = $this->manager->calculateAgeByPerson($person);
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

        $this->template->children = $children;

        $this->template->jobs = $jobs;

        $this->template->historyNotes = $historyNotes;

        $this->template->age = $age;

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addFilter('job', new JobFilter($this->getTranslator()));
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
        $this->template->addFilter('relation', new RelationFilter($this->getTranslator()));
        $this->template->addFilter('name', new NameFilter($this->getTranslator()));       
        $this->template->addFilter('wedding', new WeddingFilter($this->getTranslator()));
    }

    /**
     * @param int|null$id
     */
    public function actionAddresses($id)
    {
    }

    /**
     * @param int|null$id
     */
    public function actionNames($id)
    {
    }

    /**
     * @param int|null$id
     */
    public function actionHusbands($id)
    {
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @param int|null$id
     */
    public function actionWives($id)
    {
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @param int|null$id
     */
    public function actionMaleRelations($id)
    {
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @param int|null$id
     */
    public function actionFemaleRelations($id)
    {
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @param int|null$id
     */
    public function actionJobs($id)
    {
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

        $form->addText('nameFonetic', 'person_name_fonetic')
            ->setNullable();

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
            ->addRule($form::RANGE, 'person_age_range_error', [0, 130]);

        $form->addSelect('genusId', $this->getTranslator()->translate('person_genus'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_genus'));

        $form->addGroup('person_birth_group');

        $form->addCheckbox('hasBirthDate', 'person_has_birth_date')
            ->setOption('id', 'has-birth-date')
            ->addCondition(Form::EQUAL, true)
            ->toggle('birth-date')
            ->toggle('has-birth-year', false);

        $form->addTbDatePicker('birthDate', 'person_birth_date')
            ->setNullable()
            ->setOption('id', 'birth-date')
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addCheckbox('hasBirthYear', 'person_has_birth_year')
            ->setOption('id', 'has-birth-year')
            ->addCondition(Form::EQUAL, true)
            ->toggle('birth-year')
            ->toggle('has-birth-date', false);

        $form->addInteger('birthYear', 'person_birth_year')
            ->setNullable()
            ->setOption('id', 'birth-year');

        $form->addSelect('birthPlaceId', $this->getTranslator()->translate('person_birth_place'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_birth_place'));

        $form->addCheckbox('stillAlive', 'person_still_alive')
            ->addCondition(Form::EQUAL, true)
            ->toggle('age-group', false)
            ->toggle('death-group', false)
            ->addCondition(Form::EQUAL, true);

        $form->addGroup('person_death_group')->setOption('id', 'death-group');

        $form->addCheckbox('hasDeathDate', 'person_has_death_date')
            ->setOption('id', 'has-death-date')
            ->addCondition(Form::EQUAL, true)
            ->toggle('death-date')
            ->toggle('has-death-year', false);

        $form->addTbDatePicker('deathDate', 'person_dead_date')
            ->setNullable()
            ->setOption('id', 'death-date')
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addCheckbox('hasDeathYear', 'person_has_death_year')
            ->setOption('id', 'has-death-year')
            ->addCondition(Form::EQUAL, true)
            ->toggle('death-year')
            ->toggle('has-death-date', false);

        $form->addInteger('deathYear', 'person_death_year')
            ->setNullable()
            ->setOption('id', 'death-year');

        $form->addSelect('deathPlaceId', $this->getTranslator()->translate('person_death_place'))
            ->setOption('id', 'death-place-id')
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_death_place'));

        $form->addSelect('gravedPlaceId', $this->getTranslator()->translate('person_graved_place'))
            ->setOption('id', 'graved-place-id')
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_graved_place'));

        $form->addGroup('person_parents_group');

        $form->addSelect('fatherId', $this->getTranslator()->translate('person_father'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_father'));

        $form->addSelect('motherId', $this->getTranslator()->translate('person_mother'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_mother'));

        $form->addGroup('person_note_group');

        $form->addTextArea('note', 'person_note')
            ->setAttribute('class', ' form-control tinyMCE');

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
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

        $this->redirect(':default');
    }

    /**
     * @return PersonJobForm
     */
    public function createComponentJobsForm()
    {
        return new PersonJobForm(
            $this->getTranslator(),
            $this->manager,
            $this->person2JobManager,
            $this->jobManager
        );
    }

    /**
     * @return PersonAddressForm
     */
    public function createComponentAddressForm()
    {
        return new PersonAddressForm(
            $this->getTranslator(),
            $this->manager,
            $this->person2AddressManager,
            $this->addressManager
        );
    }

    /**
     * @return PersonMaleRelationsForm
     */
    public function createComponentMaleRelationsForm()
    {
        return new PersonMaleRelationsForm($this->getTranslator(), $this->manager, $this->relationManager);
    }

    /**
     * @return PersonFemaleRelationsForm
     */
    public function createComponentFemaleRelationsForm()
    {
        return new PersonFemaleRelationsForm($this->getTranslator(), $this->manager, $this->relationManager);
    }

    /**
     * @return PersonWivesForm
     */
    protected function createComponentWivesForm()
    {
        return new PersonWivesForm($this->getTranslator(), $this->manager, $this->weddingManager);
    }

    /**
     * @return PersonHusbandsForm
     */
    protected function createComponentHusbandsForm()
    {
        return new PersonHusbandsForm($this->getTranslator(), $this->manager, $this->weddingManager);
    }

    /**
     * @return PersonNamesForm
     */
    public function createComponentNamesForm()
    {
        return new PersonNamesForm($this->getTranslator(), $this->namesManager, $this->manager);
    }
}
