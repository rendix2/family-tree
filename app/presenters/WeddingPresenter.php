<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingPresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 1:34
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Forms\WeddingForm;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Presenters\Traits\Wedding\WeddingEditDeleteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Wedding\WeddingListDeleteModal;

/**
 * Class WeddingPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class WeddingPresenter extends BasePresenter
{
    use WeddingEditDeleteModal;
    use WeddingListDeleteModal;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * WeddingPresenter constructor.
     *
     * @param PersonManager $personManager
     * @param TownManager $townManager
     * @param WeddingFacade $weddingFacade
     * @param WeddingManager $manager
     */
    public function __construct(
        PersonManager $personManager,
        TownManager $townManager,
        WeddingFacade $weddingFacade,
        WeddingManager $manager
    ) {
        parent::__construct();

        $this->personManager = $personManager;
        $this->townManager = $townManager;
        $this->weddingManager = $manager;
        $this->weddingFacade = $weddingFacade;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $weddings = $this->weddingFacade->getAllCached();

        $this->template->weddings = $weddings;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        $this->template->addFilter('dateFT', new DurationFilter($this->getTranslator()));
        $this->template->addFilter('town', new TownFilter());
    }

    /**
     * @param int|null $id weddingId
     */
    public function actionEdit($id = null)
    {
        $husbands = $this->personManager->getMalesPairsCached($this->getTranslator());
        $wives = $this->personManager->getFemalesPairsCached($this->getTranslator());
        $towns = $this->townManager->getAllPairsCached();

        $this['form-husbandId']->setItems($husbands);
        $this['form-wifeId']->setItems($wives);
        $this['form-townId']->setItems($towns);

        if ($id !== null) {
            $wedding = $this->weddingFacade->getByPrimaryKeyCached($id);

            if (!$wedding) {
                $this->error('Item not found.');
            }

            $this['form']->setDefaults((array)$wedding);

            $this['form-husbandId']->setDefaultValue($wedding->husband->id);
            $this['form-wifeId']->setDefaultValue($wedding->wife->id);

            if ($wedding->town) {
                $this['form-townId']->setDefaultValue($wedding->town->id);
            }

            $this['form-dateSince']->setDefaultValue($wedding->duration->dateSince);
            $this['form-dateTo']->setDefaultValue($wedding->duration->dateTo);
            $this['form-untilNow']->setDefaultValue($wedding->duration->untilNow);
        }
    }

    /**
     * @param int|null $id weddingId
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $wife = null;
            $wifeWeddingAge = null;
            $husband = null;
            $husbandWeddingAge = null;
            $relationLength = null;

            $this->template->wife = null;
            $this->template->wifeWeddingAge = null;

            $this->template->husband = null;
            $this->template->husbandWeddingAge = null;
        } else {
            $wedding = $this->weddingFacade->getByPrimaryKeyCached($id);

            if (!$wedding) {
                $this->error('Item not found.');
            }

            $calcResult = $this->weddingManager->calcLengthRelation($wedding->husband, $wedding->wife, $wedding->duration, $this->getTranslator());

            $wifeWeddingAge = $calcResult['femaleRelationAge'];
            $husbandWeddingAge = $calcResult['maleRelationAge'];
            $relationLength = $calcResult['relationLength'];

            $this->template->wife = $wedding->wife;
            $this->template->wifeWeddingAge = $wifeWeddingAge;

            $this->template->husband = $wedding->husband;
            $this->template->husbandWeddingAge = $husbandWeddingAge;
        }

        $this->template->relationLength = $relationLength;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
    }

    /**
     * @param int|null $id personId
     */
    public function actionHusband($id = null)
    {
        $wife = $this->personManager->getByPrimaryKeyCached($id);

        if (!$wife) {
            $this->error('Item not found.');
        }

        $husbands = $this->personManager->getMalesPairs($this->getTranslator());
        $towns = $this->townManager->getAllPairs();

        $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

        $this['husbandForm-husbandId']->setItems($husbands);
        $this['husbandForm-wifeId']->setItems([$id => $personFilter($wife)]);
        $this['husbandForm-wifeId']->setDisabled()->setDefaultValue($id);
        $this['husbandForm-townId']->setItems($towns);
    }

    /**
     * @param int|null $id personId
     */
    public function renderHusband($id = null)
    {
        $wife = $this->personManager->getByPrimaryKeyCached($id);

        $this->template->person = $wife;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
    }

    /**
     * @param int $id personId
     */
    public function actionWife($id)
    {
        $husband = $this->personManager->getByPrimaryKeyCached($id);

        if (!$husband) {
            $this->error('Item not found.');
        }

        $wives = $this->personManager->getFemalesPairs($this->getTranslator());
        $towns = $this->townManager->getAllPairs();

        $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

        $this['wifeForm-wifeId']->setItems($wives);
        $this['wifeForm-husbandId']->setItems([$id => $personFilter($husband)]);
        $this['wifeForm-husbandId']->setDisabled()->setDefaultValue($id);
        $this['wifeForm-townId']->setItems($towns);
    }

    /**
     * @param int|null $id personId
     */
    public function renderWife($id = null)
    {
        $person = $husband = $this->personManager->getByPrimaryKeyCached($id);

        $this->template->person = $person;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
    }


    /**
     * @return Form
     */
    protected function createComponentForm()
    {
        $formFactory = new WeddingForm($this->getTranslator());

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
            $this->weddingManager->updateByPrimaryKey($id, $values);
            $this->flashMessage('item_updated', self::FLASH_SUCCESS);
        } else {
            $id = $this->weddingManager->add($values);
            $this->flashMessage('item_added', self::FLASH_SUCCESS);
        }

        $this->redirect(':edit', $id);
    }

    //// HUSBAND

    /**
     * @return Form
     */
    protected function createComponentHusbandForm()
    {
        $formFactory = new WeddingForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'saveHusbandForm'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveHusbandForm(Form $form, ArrayHash $values)
    {
        $values->wifeId = $this->getParameter('id');
        $id = $this->weddingManager->add($values);
        $this->flashMessage('item_added', self::FLASH_SUCCESS);
        $this->redirect(':edit', $id);
    }

    //// WIFE

    /**
     * @return Form
     */
    protected function createComponentWifeForm()
    {
        $formFactory = new WeddingForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'saveWifeForm'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveWifeForm(Form $form, ArrayHash $values)
    {
        $values->husbandId = $this->getParameter('id');
        $id = $this->weddingManager->add($values);
        $this->flashMessage('item_added', self::FLASH_SUCCESS);
        $this->redirect(':edit', $id);
    }
}
