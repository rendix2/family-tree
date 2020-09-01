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

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Forms\PeopleJobForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\People2AddressManager;
use Rendix2\FamilyTree\App\Managers\People2JobManager;
use Rendix2\FamilyTree\App\Managers\PeopleManager;
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
     * PeoplePresenter constructor.
     *
     * @param AddressManager $addressManager
     * @param JobManager $jobManager
     * @param PeopleManager $manager
     * @param People2AddressManager $people2AddressManager
     * @param People2JobManager $people2JobManager
     * @param NameManager $namesManager
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        AddressManager $addressManager,
        JobManager $jobManager,
        PeopleManager $manager,
        People2AddressManager $people2AddressManager,
        People2JobManager $people2JobManager,
        NameManager $namesManager,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->manager = $manager;

        $this->addressManager = $addressManager;
        $this->jobManager = $jobManager;
        $this->people2AddressManager = $people2AddressManager;
        $this->people2JobManager = $people2JobManager;
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
     * @param int $id
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
     */
    public function renderEdit($id)
    {
        $people = $this->manager->getByPrimaryKey($id);

        $addresses = $this->people2AddressManager->getFluentByLeftJoined($id)->orderBy('dateSince', \dibi::ASC);
        $names = $this->namesManager->getByPeopleId($id);
        $wives = $this->weddingManager->getALlByWifeIdJoined($id);
        $husbands = $this->weddingManager->getAllByHusbandIdJoined($id);
        $father = $this->manager->getByPrimaryKey($people->fatherId);
        $mother = $this->manager->getByPrimaryKey($people->motherId);
        $jobs = $this->people2JobManager->getAllByLeftJoined($id);

        if ($people->sex === 'm') {
            $children = $this->manager->getChildrenByFather($id);
        } elseif ($people->sex === 'f') {
            $children = $this->manager->getChildrenByMother($id);
        } else {
            throw new \Exception('Unknown Sex of people.');
        }

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addresses = $addresses;
        $this->template->names = $names;
        $this->template->wives = $wives;
        $this->template->husbands = $husbands;
        $this->template->father = $father;
        $this->template->mother = $mother;
        $this->template->children = $children;
        $this->template->jobs = $jobs;
    }

    public function actionAddresses($id)
    {

    }

    public function renderAddresses($id)
    {
        $addresses = $this->addressManager->getAll();
        $selectedAddresses = $this->people2AddressManager->getPairsByLeft($id);

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addresses = $addresses;
        $this->template->selectedAddresses = array_flip($selectedAddresses);
    }

    public function actionNames($id)
    {

    }

    public function renderNames($id)
    {

    }

    public function actionHusbands($id)
    {

    }

    public function renderHusbands($id)
    {

    }

    public function actionWives($id)
    {

    }

    public function renderWives($id)
    {

    }

    public function actionJobs($id)
    {
        $people = $this->manager->getByPrimaryKey($id);

        if (!$people) {
            $this->error('People does not found.');
        }
    }

    public function renderJobs($id)
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
        $form->addText('name', 'people_name');
        $form->addText('surname', 'people_surname');

        $form->addSelect('motherId', $this->getTranslator()->translate('people_mother'))->setTranslator(null);
        $form->addSelect('fatherId', $this->getTranslator()->translate('people_father'))->setTranslator(null);

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];

        return $form;
    }

    public function saveAddressForm(Form $form, ArrayHash $values)
    {
        $data = $form->getHttpData();
        $id = $this->getParameter('id');

        $this->people2AddressManager->deleteByLeft($id);
        $this->people2AddressManager->addByLeft($id, $data['address']);
    }

    public function createComponentAddressForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveAddressForm'];

        return $form;
    }

    public function createComponentJobsForm()
    {
        return new PeopleJobForm($this->getTranslator(), $this->jobManager, $this->people2JobManager);
    }
}
