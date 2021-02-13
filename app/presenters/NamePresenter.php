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
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\NameFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\Name\NameAddGenusModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Name\NameDeleteNameFromEditModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Name\NameDeleteNameFromListModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Name\NameDeletePersonNameModal;

/**
 * Class NamePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class NamePresenter extends BasePresenter
{
    use NameAddGenusModal;

    use NameDeleteNameFromEditModal;
    use NameDeleteNameFromListModal;

    use NameDeletePersonNameModal;

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
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * NamePresenter constructor.
     *
     * @param GenusManager $genusManager
     * @param NameFacade $nameFacade
     * @param NameManager $manager
     * @param PersonFacade $personFacade
     * @param PersonSettingsManager $personSettingsManager
     */
    public function __construct(
        GenusManager $genusManager,
        NameFacade $nameFacade,
        NameManager $manager,
        PersonFacade $personFacade,
        PersonSettingsManager $personSettingsManager
    ) {
        parent::__construct();

        $this->genusManager = $genusManager;
        $this->nameFacade = $nameFacade;
        $this->nameManager = $manager;
        $this->personFacade = $personFacade;
        $this->personSettingsManager = $personSettingsManager;
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
        $persons = $this->personSettingsManager->getAllPairsCached($this->getTranslator());
        $genuses = $this->genusManager->getPairsCached('surname');

        $this['nameForm-personId']->setItems($persons);
        $this['nameForm-genusId']->setItems($genuses);

        if ($id !== null) {
            $name = $this->nameFacade->getByPrimaryKeyCached($id);

            $this['nameForm']->setDefaults((array) $name);
            $this['nameForm-personId']->setDefaultValue($name->person->id);
            $this['nameForm-genusId']->setDefaultValue($name->genus->id);
            $this['nameForm-dateSince']->setDefaultValue($name->duration->dateSince);
            $this['nameForm-dateTo']->setDefaultValue($name->duration->dateTo);
            $this['nameForm-untilNow']->setDefaultValue($name->duration->untilNow);
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

        $this->template->addFilter('genus', new GenusFilter());
        $this->template->addFilter('name', new NameFilter());
        $this->template->addFilter('duration', new DurationFilter($this->getTranslator()));
    }

    /**
     * @return Form
     */
    protected function createComponentNameForm()
    {
        $formFactory = new NameForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'nameFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function nameFormSuccess(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->nameManager->updateByPrimaryKey($id, $values);

            $this->flashMessage('name_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->nameManager->add($values);

            $this->flashMessage('name_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Name:edit', $id);
    }
}
