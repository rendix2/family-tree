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

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\GenusFilter;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\NameForm;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Facades\NameFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\Genus\AddGenusModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Name\NameEditDeleteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Name\NameListDeleteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Name\NamePersonNameDeleteModal;

/**
 * Class NamePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class NamePresenter extends BasePresenter
{
    use NameEditDeleteModal;
    use NameListDeleteModal;

    use NamePersonNameDeleteModal;

    use AddGenusModal;

    /**
     * @var NameFacade $nameFacade
     */
    private $nameFacade;

    /**
     * @var NameManager $nameManager
     */
    private $nameManager;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * NamePresenter constructor.
     *
     * @param GenusManager $genusManager
     * @param NameManager $manager
     * @param NameFacade $nameFacade
     * @param PersonFacade $personFacade
     * @param PersonManager $personManager
     */
    public function __construct(
        GenusManager $genusManager,
        NameManager $manager,
        NameFacade $nameFacade,
        PersonFacade $personFacade,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->nameFacade = $nameFacade;
        $this->nameManager = $manager;
        $this->genusManager = $genusManager;
        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $names = $this->nameFacade->getAllCached();

        $this->template->names = $names;

        $this->template->addFilter('duration', new DurationFilter($this->getTranslator()));
        $this->template->addFilter('genus', new GenusFilter());
        $this->template->addFilter('name', new NameFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
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
        $this->template->addFilter('duration', new DurationFilter($this->getTranslator()));
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
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveForm(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->nameManager->updateByPrimaryKey($id, $values);
            $this->flashMessage('item_updated', self::FLASH_SUCCESS);
        } else {
            $id = $this->nameManager->add($values);
            $this->flashMessage('item_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Name:edit', $id);
    }
}
