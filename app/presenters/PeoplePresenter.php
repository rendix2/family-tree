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

use Exception;
use Nette\Application\UI\Form;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Forms\PeopleAddressForm;
use Rendix2\FamilyTree\App\Forms\PeopleFemaleRelationsForm;
use Rendix2\FamilyTree\App\Forms\PeopleJobForm;
use Rendix2\FamilyTree\App\Forms\PeopleMaleRelationsForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
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
     * PeoplePresenter constructor.
     *
     * @param AddressManager $addressManager
     * @param JobManager $jobManager
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
        $this->people2AddressManager = $people2AddressManager;
        $this->people2JobManager = $people2JobManager;
        $this->relationManager = $relationManager;
        $this->namesManager = $namesManager;
        $this->weddingManager = $weddingManager;
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

        $this['form-fatherId']->setItems($males);
        $this['form-motherId']->setItems($females);

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
            $wives = $this->weddingManager->getALlByWifeIdJoined($id);
            $husbands = $this->weddingManager->getAllByHusbandIdJoined($id);
            $father = $this->manager->getByPrimaryKey($people->fatherId);
            $mother = $this->manager->getByPrimaryKey($people->motherId);
            $jobs = $this->people2JobManager->getAllByLeftJoined($id);
            $maleRelations = $this->relationManager->getByMaleIdJoined($people->id);
            $femaleRelations = $this->relationManager->getByFemaleIdJoined($people->id);

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
        $people = $this->manager->getByPrimaryKey($id);

        if (!$people) {
            $this->error('People was not found.');
        }
    }

    /**
     * @param int|null$id
     */
    public function actionNames($id)
    {
        $people = $this->manager->getByPrimaryKey($id);

        if (!$people) {
            $this->error('People was not found.');
        }
    }

    /**
     * @param int|null$id
     */
    public function actionHusbands($id)
    {
        $people = $this->manager->getByPrimaryKey($id);

        if (!$people) {
            $this->error('People was not found.');
        }
    }

    /**
     * @param int|null$id
     */
    public function actionWives($id)
    {
        $people = $this->manager->getByPrimaryKey($id);

        if (!$people) {
            $this->error('People was not found.');
        }
    }

    /**
     * @param int|null$id
     */
    public function actionMaleRelations($id)
    {
        $people = $this->manager->getByPrimaryKey($id);

        if (!$people) {
            $this->error('People was not found.');
        }
    }

    /**
     * @param int|null$id
     */
    public function actionFemaleRelations($id)
    {
        $people = $this->manager->getByPrimaryKey($id);

        if (!$people) {
            $this->error('People was not found.');
        }
    }

    /**
     * @param int|null$id
     */
    public function actionJobs($id)
    {
        $people = $this->manager->getByPrimaryKey($id);

        if (!$people) {
            $this->error('People does not found.');
        }
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();
        $form->addText('name', 'people_name')->setRequired("people_name_required");
        $form->addText('surname', 'people_surname')->setRequired("people_surname_required");
        $form->addRadioList('sex', 'people_gender', ['m' => 'people_male', 'f' => 'people_female'])
            ->setRequired("people_gender_required");

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
            ->setPrompt($this->getTranslator()->translate('people_selected_father'));

        $form->addSelect('motherId', $this->getTranslator()->translate('people_mother'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('people_selected_mother'));

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];

        return $form;
    }

    /**
     * @return PeopleJobForm
     */
    public function createComponentJobsForm()
    {
        return new PeopleJobForm($this->getTranslator(), $this->jobManager, $this->people2JobManager);
    }

    /**
     * @return PeopleAddressForm
     */
    public function createComponentAddressForm()
    {
        return new PeopleAddressForm($this->getTranslator(), $this->addressManager, $this->people2AddressManager);
    }

    /**
     * @return PeopleMaleRelationsForm
     */
    public function createComponentMaleRelationsForm()
    {
        return new PeopleMaleRelationsForm($this->getTranslator(), $this->manager, $this->relationManager);
    }

    /**
     * @return PeopleFemaleRelationsForm
     */
    public function createComponentFemaleRelationsForm()
    {
        return new PeopleFemaleRelationsForm($this->getTranslator(), $this->manager, $this->relationManager);
    }
}
