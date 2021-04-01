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
use Rendix2\FamilyTree\App\Controls\Forms\NameForm;
use Rendix2\FamilyTree\App\Controls\Modals\Name\Container\NameModalContainer;

use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\NameFacade;

/**
 * Class NamePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class NamePresenter extends BasePresenter
{
    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var NameFacade $nameFacade
     */
    private $nameFacade;

    /**
     * @var NameForm $nameForm
     */
    private $nameForm;

    /**
     * @var NameManager $nameManager
     */
    private $nameManager;

    /**
     * @var NameModalContainer $nameModalContainer
     */
    private $nameModalContainer;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * NamePresenter constructor.
     *
     * @param GenusManager          $genusManager
     * @param NameFacade            $nameFacade
     * @param NameForm              $nameForm
     * @param NameManager           $nameManager
     * @param NameModalContainer    $nameModalContainer
     * @param PersonSettingsManager $personSettingsManager
     */
    public function __construct(
        GenusManager $genusManager,
        NameFacade $nameFacade,
        NameForm $nameForm,
        NameManager $nameManager,
        NameModalContainer $nameModalContainer,
        PersonSettingsManager $personSettingsManager
    ) {
        parent::__construct();

        $this->genusManager = $genusManager;
        $this->nameFacade = $nameFacade;
        $this->nameForm = $nameForm;
        $this->nameManager = $nameManager;
        $this->nameModalContainer = $nameModalContainer;
        $this->personSettingsManager = $personSettingsManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $names = $this->nameFacade->getAllCached();

        $this->template->names = $names;
    }

    /**
     * @param int|null $id nameId
     */
    public function actionEdit($id = null)
    {
        $persons = $this->personSettingsManager->getAllPairsCached();
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
            $personNames = $this->nameFacade->getByPersonIdCached($name->person->id);
        } else {
            $person = null;
            $name = null;
            $personNames = [];
        }

        $this->template->name = $name;
        $this->template->person = $person;
        $this->template->personNames = $personNames;
    }

    /**
     * @return Form
     */
    protected function createComponentNameForm()
    {
        $form = $this->nameForm->create();
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

    public function createComponentNameAddGenusModal()
    {
        return $this->nameModalContainer->getNameAddGenusModalFactory()->create();
    }

    public function createComponentNameDeleteNameFromEditModal()
    {
        return $this->nameModalContainer->getNameDeleteNameFromEditModalFactory()->create();
    }

    public function createComponentNameDeleteNameFromListModal()
    {
        return $this->nameModalContainer->getNameDeleteNameFromListModalFactory()->create();
    }

    public function createComponentNameDeletePersonNameModal()
    {
        return $this->nameModalContainer->getNameDeletePersonNameModalFactory()->create();
    }
}
