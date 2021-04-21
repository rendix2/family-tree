<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddParentMaleModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:59
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
 * Class PersonAddParentPartnerMaleModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddParentPartnerMaleModal extends Control
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
     * PersonAddParentPartnerMaleModal constructor.
     *
     * @param PersonManager       $personManager
     * @param PersonUpdateService $personUpdateService
     * @param RelationManager     $relationManager
     * @param RelationForm        $relationForm
     */
    public function __construct(
        PersonManager $personManager,
        PersonUpdateService $personUpdateService,
        RelationManager $relationManager,
        RelationForm $relationForm
    ) {
        parent::__construct();

        $this->relationForm = $relationForm;

        $this->personManager = $personManager;
        $this->personUpdateService = $personUpdateService;
        $this->relationManager = $relationManager;
    }

    public function __destruct()
    {
        $this->relationForm = null;

        $this->personManager = null;

        $this->relationManager = null;
        $this->personUpdateService = null;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddParentPartnerMaleForm']->render();
    }

    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddParentMalePartner($personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $persons = $this->personManager->select()->getSettingsCachedManager()->getAllPairs();

        $this['personAddParentPartnerMaleForm-_femaleId']->setDefaultValue($personId);
        $this['personAddParentPartnerMaleForm-femaleId']->setItems($persons)
            ->setDisabled()
            ->setDefaultValue($personId);

        $this['personAddParentPartnerMaleForm-maleId']->setItems($persons);

        $presenter->template->modalName = 'personAddParentMalePartner';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddParentPartnerMaleForm()
    {
        $form = $this->relationForm->create();

        $form->addHidden('_femaleId');

        $form->onAnchor[] = [$this, 'personAddParentPartnerMaleFormAnchor'];
        $form->onValidate[] = [$this, 'personAddParentPartnerMaleFormValidate'];
        $form->onSuccess[] = [$this, 'personAddParentPartnerMaleFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddParentPartnerMaleFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddParentPartnerMaleFormValidate(Form $form)
    {
        $persons = $this->personManager->select()->getSettingsCachedManager()->getAllPairs();

        $maleControl = $form->getComponent('maleId');
        $maleControl->setItems($persons)
            ->validate();

        $femaleHiddenControl = $form->getComponent('_femaleId');

        $femaleControl = $form->getComponent('femaleId');
        $femaleControl->setItems($persons)
            ->setValue($femaleHiddenControl->getValue())
            ->validate();

        $form->removeComponent($femaleHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddParentPartnerMaleFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->relationManager->insert()->insert((array) $values);

        $this->personUpdateService->prepareRelations($presenter, $values->maleId);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('relation_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('father_relations');
        $presenter->redrawControl('mother_relations');
    }
}
