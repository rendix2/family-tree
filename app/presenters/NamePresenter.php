<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NamePresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 2:09
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Dibi\Row;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\DateFilter;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Presenters\Traits\Name\NamePersonNameDeleteModal;

/**
 * Class NamePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class NamePresenter extends BasePresenter
{
    use CrudPresenter {
        actionEdit as traitActionEdit;
    }

    use NamePersonNameDeleteModal;

    /**
     * @var NameManager $manager
     */
    private $manager;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var Row $person
     */
    private $person;

    /**
     * NamePresenter constructor.
     *
     * @param GenusManager $genusManager
     * @param NameManager $manager
     * @param PersonManager $personManager
     */
    public function __construct(
        GenusManager $genusManager,
        NameManager $manager,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->manager = $manager;
        $this->genusManager = $genusManager;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $names = $this->manager->getAll();

        foreach ($names as $name) {
            $person = $this->personManager->getByPrimaryKey($name->personId);

            $name->person = $person;
        }

        $this->template->names = $names;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
        $this->template->addFilter('name', new NameFilter());
    }

    /**
     * @param int|null $id nameId
     */
    public function actionEdit($id = null)
    {
        $persons = $this->personManager->getAllPairs($this->getTranslator());
        $genuses = $this->genusManager->getPairs('surname');

        $this['form-personId']->setItems($persons);
        $this['form-genusId']->setItems($genuses);

        $this->traitActionEdit($id);
    }

    /**
     * @param int|null $id nameId
     */
    public function renderEdit($id = null)
    {
        if ($id) {
            $person = $this->personManager->getByPrimaryKey($this->item->personId);
            $name = $this->manager->getByPrimaryKey($id);
            $personNames = $this->manager->getByPersonId($this->item->personId);
        } else {
            $person = null;
            $name = null;
            $personNames = [];
        }

        $this->template->name = $name;
        $this->template->person = $person;
        $this->template->personNames = $personNames;

        $this->template->addFilter('name', new NameFilter());
        $this->template->addFilter('dateFT', new DateFilter($this->getTranslator()));
    }

    /**
     * @param int|null $id personId
     */
    public function actionName($id = null)
    {
        $person  = $this->personManager->getByPrimaryKey($id);

        if (!$person) {
            $this->error('Item not found.');
        }

        $this->person = $person;

        $genuses = $this->genusManager->getPairs('surname');

        $personFilter = new PersonFilter($this->getTranslator());

        $this['nameForm-personId']->setItems([$id => $personFilter($person)]);
        $this['nameForm-personId']->setDisabled()->setValue($id);
        $this['nameForm-genusId']->setItems($genuses);
    }

    /**
     * @param int|null $id personId
     */
    public function renderName($id = null)
    {
        $this->template->person = $this->person;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @return Form
     */
    private function createForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addSelect('personId', $this->getTranslator()->translate('name_person'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('name_select_person'))
            ->setRequired('name_person_required');

        $form->addText('name', 'name_name')
            ->setRequired('name_name_required');

        $form->addText('nameFonetic', 'name_name_fonetic')
            ->setNullable();

        $form->addText('surname', 'name_surname')
            ->setRequired('name_surname_required');

        $form->addSelect('genusId', $this->getTranslator()->translate('name_genus'))
            ->setPrompt($this->getTranslator()->translate('name_select_genus'))
            ->setTranslator(null)
            ->setRequired('name_genus_required');

        $form->addCheckbox('untilNow', 'name_until_now')
            ->addCondition(Form::EQUAL, false)
            ->toggle('date-to');

        $form->addTbDatePicker('dateSince', 'date_since')
            ->setNullable()
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addTbDatePicker('dateTo', 'date_to')
            ->setNullable()
            ->setOption('id', 'date-to')
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addSubmit('send', 'save');

        return $form;
    }

    /**
     * @return Form
     */
    protected function createComponentForm()
    {
        $form = $this->createForm();

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }

    /**
     * @return Form
     */
    protected function createComponentNameForm()
    {
        $form = $this->createForm();

        $form->onSuccess[] = [$this, 'saveNameForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveNameForm(Form $form, ArrayHash $values)
    {
        $values->personId = $this->getParameter('id');

        $id = $this->manager->add($values);

        $this->flashMessage('item_added', self::FLASH_SUCCESS);
        $this->redirect(':edit', $id);
    }
}
