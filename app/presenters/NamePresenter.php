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
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\GenusFilter;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\NameForm;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Facades\NameFacade;
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
     * @var NameFacade $nameFacade
     */
    private $nameFacade;

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
     * @param NameFacade $nameFacade
     * @param PersonManager $personManager
     */
    public function __construct(
        GenusManager $genusManager,
        NameManager $manager,
        NameFacade $nameFacade,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->nameFacade = $nameFacade;
        $this->manager = $manager;
        $this->genusManager = $genusManager;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $names = $this->nameFacade->getAllCached();

        $this->template->names = $names;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        $this->template->addFilter('name', new NameFilter());
        $this->template->addFilter('genus', new GenusFilter());
    }

    /**
     * @param int|null $id nameId
     */
    public function actionEdit($id = null)
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());
        $genuses = $this->genusManager->getPairsCached('surname');

        $this['form-personId']->setItems($persons);
        $this['form-genusId']->setItems($genuses);

        if ($id !== null) {
            $name = $this->nameFacade->getByPrimaryKeyCached($id);

            if (!$name) {
                $this->error('Item not found.');
            }

            $this['form']->setDefaults((array)$name);
            $this['form-personId']->setDefaultValue($name->person->id);
            $this['form-genusId']->setDefaultValue($name->genus->id);
            $this['form-dateSince']->setDefaultValue($name->duration->dateSince);
            $this['form-dateTo']->setDefaultValue($name->duration->dateTo);
            $this['form-untilNow']->setDefaultValue($name->duration->untilNow);
        }
    }

    /**
     * @param int|null $id nameId
     */
    public function renderEdit($id = null)
    {
        if ($id) {
            $name = $this->nameFacade->getByPrimaryKeyCached($id);

            $person = $name->person;
            $personNames = $this->nameFacade->getByPersonCached($name->person->id);
        } else {
            $person = null;
            $name = null;
            $personNames = [];
        }

        $this->template->name = $name;
        $this->template->person = $person;
        $this->template->personNames = $personNames;

        $this->template->addFilter('name', new NameFilter());
        $this->template->addFilter('dateFT', new DurationFilter($this->getTranslator()));
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

        $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

        $this['nameForm-personId']->setItems([$id => $personFilter($person)]);
        $this['nameForm-personId']->setDisabled()->setDefaultValue($id);
        $this['nameForm-genusId']->setItems($genuses);
    }

    /**
     * @param int|null $id personId
     */
    public function renderName($id = null)
    {
        $this->template->person = $this->person;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
    }

    /**
     * @return Form
     */
    protected function createComponentForm()
    {
        $formFactory = new NameForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'saveForm'];

        return $form;
    }

    /**
     * @return Form
     */
    protected function createComponentNameForm()
    {
        $formFactory = new NameForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'saveNameForm'];

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
