<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPartnerMaleModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:01
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\RelationForm;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Managers\RelationManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Services\PersonUpdateService;

/**
 * Class PersonAddPartnerMaleModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddPartnerMaleModal extends Control
{
    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonUpdateService $personUpdateService
     */
    private $personUpdateService;

    /**
     * @var RelationForm $relationForm
     */
    private $relationForm;

    /**
     * @var RelationManager $relationManager
     */
    private $relationManager;

    /**
     * PersonAddPartnerMaleModal constructor.
     *
     * @param PersonManager       $personManager
     * @param PersonUpdateService $personUpdateService
     * @param RelationForm        $relationForm
     * @param RelationManager     $relationManager
     */
    public function __construct(
        PersonManager $personManager,
        PersonUpdateService $personUpdateService,
        RelationForm $relationForm,
        RelationManager $relationManager
    ) {
        parent::__construct();

        $this->relationForm = $relationForm;

        $this->personManager = $personManager;

        $this->relationManager = $relationManager;

        $this->personUpdateService = $personUpdateService;
    }

    public function __destruct()
    {
        $this->relationForm = null;

        $this->personManager = null;
        $this->personUpdateService = null;
        $this->relationManager = null;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddPartnerMaleForm']->render();
    }

    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddPartnerMale($personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $males = $this->personManager->select()->getSettingsCachedManager()->getMalesPairs();
        $persons = $this->personManager->select()->getSettingsCachedManager()->getAllPairs();

        $this['personAddPartnerMaleForm-maleId']->setItems($males);
        $this['personAddPartnerMaleForm-_femaleId']->setDefaultValue($personId);
        $this['personAddPartnerMaleForm-femaleId']->setItems($persons)
            ->setDisabled()
            ->setDefaultValue($personId);

        $presenter->template->modalName = 'personAddPartnerMale';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddPartnerMaleForm()
    {
        $form = $this->relationForm->create();

        $form->addHidden('_femaleId');

        $form->onAnchor[] = [$this, 'personAddPartnerMaleFormFormAnchor'];
        $form->onValidate[] = [$this, 'personAddPartnerMaleFormValidate'];
        $form->onSuccess[] = [$this, 'personAddPartnerMaleFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddPartnerMaleFormFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddPartnerMaleFormValidate(Form $form)
    {
        $males = $this->personManager->select()->getCachedManager()->getMalesPairs();
        $persons = $this->personManager->select()->getCachedManager()->getAllPairs();

        $maleControl = $form->getComponent('maleId');
        $maleControl->setItems($males)
            ->validate();

        $femaleHiddenControl = $form->getComponent('_femaleId');

        $femaleControl = $form->getComponent('femaleId');
        $femaleControl->setItems($persons);
        $femaleControl->setValue($femaleHiddenControl->getValue())
            ->validate();

        $form->removeComponent($femaleHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddPartnerMaleFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->relationManager->insert()->insert((array) $values);

        $this->personUpdateService->prepareRelations($presenter, $values->femaleId);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('relation_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('relation_males');
        $presenter->redrawControl('flashes');
    }
}
