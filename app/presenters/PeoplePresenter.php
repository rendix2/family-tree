<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PeoplePresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 1:56
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Dibi\Row;
use Exception;
use Nette\Application\UI\Form;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Forms\PersonAddressForm;
use Rendix2\FamilyTree\App\Forms\PersonFemaleRelationsForm;
use Rendix2\FamilyTree\App\Forms\PersonHusbandsForm;
use Rendix2\FamilyTree\App\Forms\PersonJobForm;
use Rendix2\FamilyTree\App\Forms\PersonMaleRelationsForm;
use Rendix2\FamilyTree\App\Forms\PersonWivesForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\People2AddressManager;
use Rendix2\FamilyTree\App\Managers\People2JobManager;
use Rendix2\FamilyTree\App\Managers\PeopleManager;
use Rendix2\FamilyTree\App\Managers\RelationManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;

/**
 * Class PeoplePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class PeoplePresenter extends BasePresenter
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
     * @var People2AddressManager $people2AddressManager
     */
    private $people2AddressManager;

    /**
     * @var NameManager $namesManager
     */
    private $namesManager;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var People2JobManager $people2JobManager
     */
    private $people2JobManager;

    /**
     * @var RelationManager $relationManager
     */
    private $relationManager;

    /**
     * @var Row $person
     */
    private $person;

    /**
     * PeoplePresenter constructor.
     *
     * @param AddressManager $addressManager
     * @param JobManager $jobManager
     * @param GenusManager $genusManager
     * @param PeopleManager $manager
     * @param People2AddressManager $people2AddressManager
     * @param People2JobManager $people2JobManager
     * @param RelationManager $relationManager
     * @param NameManager $namesManager
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        AddressManager $addressManager,
        JobManager $jobManager,
        GenusManager $genusManager,
        PeopleManager $manager,
        People2AddressManager $people2AddressManager,
        People2JobManager $people2JobManager,
        RelationManager $relationManager,
        NameManager $namesManager,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->manager = $manager;

        $this->addressManager = $addressManager;
        $this->jobManager = $jobManager;
        $this->genusManager = $genusManager;
        $this->people2AddressManager = $people2AddressManager;
        $this->people2JobManager = $people2JobManager;
        $this->relationManager = $relationManager;
        $this->namesManager = $namesManager;
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
        $peoples = $this->manager->getAllFluent()->fetchAll();

        $this->template->peoples = $peoples;
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

            $children = [];
            $jobs = [];
        } else {
            $people = $this->manager->getByPrimaryKey($id);

            $addresses = $this->people2AddressManager->getFluentByLeftJoined($id)->orderBy('dateSince', \dibi::ASC);
            $names = $this->namesManager->getByPeopleId($id);
            $husbands = $this->weddingManager->getAllByWifeIdJoined($id);
            $wives = $this->weddingManager->getAllByHusbandIdJoined($id);
            $father = $this->manager->getByPrimaryKey($people->fatherId);
            $mother = $this->manager->getByPrimaryKey($people->motherId);
            $jobs = $this->people2JobManager->getAllByLeftJoined($id);
            $femaleRelations = $this->relationManager->getByMaleIdJoined($people->id);
            $maleRelations = $this->relationManager->getByFemaleIdJoined($people->id);

            if ($people->sex === 'm') {
                $children = $this->manager->getChildrenByFather($id);
            } elseif ($people->sex === 'f') {
                $children = $this->manager->getChildrenByMother($id);
            } else {
                throw new Exception('Unknown Sex of people.');
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

        $this->template->children = $children;

        $this->template->jobs = $jobs;
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

        $form->addText('name', 'people_name')
            ->setRequired('people_name_required');

        $form->addText('surname', 'people_surname')
            ->setRequired('people_surname_required');

        $form->addRadioList('sex', 'people_gender', ['m' => 'people_male', 'f' => 'people_female'])
            ->setRequired('people_gender_required');

        $form->addTbDatePicker('birthDate', 'people_birth_date')
            ->setNullable()
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addTbDatePicker('deathDate', 'people_dead_date')
            ->setNullable()
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addSelect('fatherId', $this->getTranslator()->translate('people_father'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('people_select_father'));

        $form->addSelect('motherId', $this->getTranslator()->translate('people_mother'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('people_select_mother'));

        $form->addSelect('genusId', $this->getTranslator()->translate('people_genus'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('people_select_genus'));

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }

    /**
     * @return PersonJobForm
     */
    public function createComponentJobsForm()
    {
        return new PersonJobForm(
            $this->getTranslator(),
            $this->manager,
            $this->people2JobManager,
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
            $this->people2AddressManager,
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
}
