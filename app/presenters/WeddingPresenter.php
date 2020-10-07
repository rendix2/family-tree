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
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\WeddingHusbandForm;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;

/**
 * Class WeddingPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class WeddingPresenter extends BasePresenter
{
    use CrudPresenter {
        actionEdit as traitActionEdit;
    }

    /**
     * @var WeddingManager $manager
     */
    private $manager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * WeddingPresenter constructor.
     *
     * @param PersonManager $personManager
     * @param TownManager $townManager
     * @param WeddingManager $manager
     */
    public function __construct(
        PersonManager $personManager,
        TownManager $townManager,
        WeddingManager $manager
    ) {
        parent::__construct();

        $this->manager = $manager;
        $this->personManager = $personManager;
        $this->townManager = $townManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $weddings = $this->manager->getAll();

        foreach ($weddings as $wedding) {
            $husband = $this->personManager->getByPrimaryKey($wedding->husbandId);
            $wife = $this->personManager->getByPrimaryKey($wedding->wifeId);

            $wedding->husband = $husband;
            $wedding->wife = $wife;
        }

        $this->template->weddings = $weddings;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $husbands = $this->personManager->getMalesPairs($this->getTranslator());
        $wives = $this->personManager->getFemalesPairs($this->getTranslator());
        $towns = $this->townManager->getPairs('name');

        $this['form-husbandId']->setItems($husbands);
        $this['form-wifeId']->setItems($wives);
        $this['form-townId']->setItems($towns);

        $this->traitActionEdit($id);
    }

    /**
     * @param int $id
     */
    public function actionHusband($id)
    {
        $wife = $this->personManager->getByPrimaryKey($id);

        if (!$wife) {
            $this->error('Item not found');
        }

        $husbands = $this->personManager->getMalesPairs($this->getTranslator());
        $towns = $this->townManager->getPairs('name');

        $personFilter = new PersonFilter($this->getTranslator());

        $this['husbandForm-husbandId']->setItems($husbands);
        $this['husbandForm-wifeId']->setItems([$id => $personFilter($wife)]);
        $this['husbandForm-wifeId']->setDisabled()->setValue($id);
        $this['husbandForm-townId']->setItems($towns);
    }

    /**
     * @param int $id
     */
    public function actionWife($id)
    {
        $husband = $this->personManager->getByPrimaryKey($id);

        if (!$husband) {
            $this->error('Item not found');
        }

        $wives = $this->personManager->getFemalesPairs($this->getTranslator());
        $towns = $this->townManager->getPairs('name');

        $personFilter = new PersonFilter($this->getTranslator());

        $this['wifeForm-wifeId']->setItems($wives);
        $this['wifeForm-husbandId']->setItems([$id => $personFilter($husband)]);
        $this['wifeForm-husbandId']->setDisabled()->setValue($id);
        $this['wifeForm-townId']->setItems($towns);
    }

    private function createForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();
        $form->addSelect('husbandId', $this->getTranslator()->translate('wedding_husband'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('wedding_select_husband'))
            ->setRequired('wedding_husband_required');

        $form->addSelect('wifeId', $this->getTranslator()->translate('wedding_wife'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('wedding_select_wife'))
            ->setRequired('wedding_wife_required');

        $form->addCheckbox('untilNow', 'wedding_until_now')
            ->addCondition(Form::EQUAL, false)
            ->toggle('date-to');

        $form->addTbDatePicker('dateSince', 'date_since')
            ->setNullable()
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addTbDatePicker('dateTo', 'date_to')
            ->setOption('id', 'date-to')
            ->setNullable()
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addSelect('townId', $this->getTranslator()->translate('wedding_town'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('wedding_select_town'));

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

    //// HUSBAND

    /**
     * @return Form
     */
    protected function createComponentHusbandForm()
    {
        $form = $this->createForm();

        $form->onSuccess[] = [$this, 'saveHusbandForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveHusbandForm(Form $form, ArrayHash $values)
    {
        $values->wifeId = $this->getParameter('id');
        $id = $this->manager->add($values);
        $this->flashMessage('item_added', self::FLASH_SUCCESS);
        $this->redirect(':edit', $id);
    }

    //// WIFE

    /**
     * @return Form
     */
    protected function createComponentWifeForm()
    {
        $form = $this->createForm();

        $form->onSuccess[] = [$this, 'saveWifeForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveWifeForm(Form $form, ArrayHash $values)
    {
        $values->husbandId = $this->getParameter('id');
        $id = $this->manager->add($values);
        $this->flashMessage('item_added', self::FLASH_SUCCESS);
        $this->redirect(':edit', $id);
    }
}
