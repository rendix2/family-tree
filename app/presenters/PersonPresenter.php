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
use Rendix2\FamilyTree\App\Managers\People2AddressManager;
use Rendix2\FamilyTree\App\Managers\People2JobManager;
use Rendix2\FamilyTree\App\Managers\PeopleManager;
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
     * @var PeopleManager $manager
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
     * @var People2AddressManager $person2AddressManager
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
     * @var People2JobManager $person2JobManager
     */
    private $person2JobManager;

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
     * @param PeopleManager $manager
     * @param People2AddressManager $person2AddressManager
     * @param People2JobManager $person2JobManager
     * @param RelationManager $relationManager
     * @param NameManager $namesManager
     * @param NoteHistoryManager $noteHistoryManager
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        AddressManager $addressManager,
        JobManager $jobManager,
        GenusManager $genusManager,
        PeopleManager $manager,
        People2AddressManager $person2AddressManager,
        People2JobManager $person2JobManager,
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
        $this->flashMessage('People_deleted');
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

        $this['form-fatherId']->setItems($males);
        $this['form-motherId']->setItems($females);
        $this['form-genusId']->setItems($genuses);

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

            if ($person->sex === 'm') {
                $children = $this->manager->getChildrenByFather($id);
            } elseif ($person->sex === 'f') {
                $children = $this->manager->getChildrenByMother($id);
            } else {
                throw new Exception('Unknown Sex of person.');
            }
        }

        $this->template->addFilter('address', new AddressFilter());

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
    }

    /**
     * @param int|null$id
     */
    public function actionWives($id)
    {
    }

    /**
     * @param int|null$id
     */
    public function actionMaleRelations($id)
    {
    }

    /**
     * @param int|null$id
     */
    public function actionFemaleRelations($id)
    {
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

        $form->addText('name', 'person_name')
            ->setRequired('person_name_required');

        $form->addText('nameFonetic', 'person_name_fonetic')
            ->setNullable();

        $form->addText('surname', 'person_surname')
            ->setRequired('person_surname_required');

        $form->addRadioList('sex', 'person_gender', ['m' => 'person_male', 'f' => 'person_female'])
            ->setRequired('person_gender_required');

        $form->addCheckbox('hasBirthDate', 'person_has_birth_date')
            ->addCondition(Form::EQUAL, true)
            ->toggle('birth-date');

        $form->addTbDatePicker('birthDate', 'person_birth_date')
            ->setNullable()
            ->setOption('id', 'birth-date')
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addCheckbox('hasBirthYear', 'person_has_birth_year')
            ->addCondition(Form::EQUAL, true)
            ->toggle('birth-year');

        $form->addInteger('birthYear', 'person_birth_year')
            ->setNullable()
            ->setOption('id', 'birth-year');

        $form->addCheckbox('hasDeathDate', 'person_has_death_date')
            ->addCondition(Form::EQUAL, true)
            ->toggle('death-date');

        $form->addTbDatePicker('deathDate', 'person_dead_date')
            ->setNullable()
            ->setOption('id', 'death-date')
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addCheckbox('hasDeathYear', 'person_has_death_year')
            ->addCondition(Form::EQUAL, true)
            ->toggle('death-year');

        $form->addInteger('deathYear', 'person_death_year')
            ->setNullable()
            ->setOption('id', 'death-year');

        $form->addSelect('fatherId', $this->getTranslator()->translate('person_father'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_father'));

        $form->addSelect('motherId', $this->getTranslator()->translate('person_mother'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_mother'));

        $form->addSelect('genusId', $this->getTranslator()->translate('person_genus'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('person_select_genus'));

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
        } else {
            $id = $this->manager->add($values);
        }

        $this->flashMessage('item_saved', 'success');
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
