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
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\RelationForm;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Managers\RelationManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonPrepareMethods;

/**
 * Class PersonAddParentPartnerMaleModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddParentPartnerMaleModal extends Control
{

    use PersonPrepareMethods;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var RelationManager $relationManager
     */
    private $relationManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * PersonAddParentPartnerMaleModal constructor.
     *
     * @param PersonSettingsManager $personSettingsManager
     * @param RelationManager $relationManager
     * @param ITranslator $translator
     */
    public function __construct(
        PersonSettingsManager $personSettingsManager,
        RelationManager $relationManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->personSettingsManager = $personSettingsManager;
        $this->relationManager = $relationManager;
        $this->translator = $translator;
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

        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);

        $this['personAddParentPartnerMaleForm-_femaleId']->setDefaultValue($personId);
        $this['personAddParentPartnerMaleForm-femaleId']->setItems($persons)
            ->setDisabled()
            ->setDefaultValue($personId);

        $this['personAddParentPartnerMaleForm-maleId']->setItems($persons);

        $this->presenter->template->modalName = 'personAddParentMalePartner';

        $this->presenter->payload->showModal = true;

        $this->presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddParentPartnerMaleForm()
    {
        $formFactory = new RelationForm($this->translator);

        $form = $formFactory->create();
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
        $this->presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddParentPartnerMaleFormValidate(Form $form)
    {
        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);

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

        $this->relationManager->add($values);

        $this->prepareRelations($values->maleId);

        $this->presenter->payload->showModal = false;

        $this->presenter->flashMessage('relation_added', BasePresenter::FLASH_SUCCESS);

        $this->presenter->redrawControl('flashes');
        $this->presenter->redrawControl('father_relations');
        $this->presenter->redrawControl('mother_relations');
    }
}
