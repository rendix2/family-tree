<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddParentPartnerFemaleModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:58
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Forms\RelationForm;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Managers\RelationManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonPrepareMethods;

/**
 * Class PersonAddParentPartnerFemaleModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddParentPartnerFemaleModal extends Control
{
    use PersonPrepareMethods;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var RelationManager $relationManager
     */
    private $relationManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * PersonAddParentPartnerFemaleModal constructor.
     * @param PersonFacade $personFacade
     * @param RelationManager $relationManager
     * @param ITranslator $translator
     * @param PersonManager $personManager
     * @param PersonSettingsManager $personSettingsManager
     */
    public function __construct(
        PersonFacade $personFacade,
        RelationManager $relationManager,
        ITranslator $translator,
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager
    ) {
        parent::__construct();

        $this->personFacade = $personFacade;
        $this->relationManager = $relationManager;
        $this->translator = $translator;
        $this->personManager = $personManager;
        $this->personSettingsManager = $personSettingsManager;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddParentPartnerFemaleForm']->render();
    }

    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddParentPartnerFemale($personId)
    {
        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);

        $this['personAddParentPartnerFemaleForm-_maleId']->setDefaultValue($personId);
        $this['personAddParentPartnerFemaleForm-maleId']->setItems($persons)
            ->setDisabled()
            ->setDefaultValue($personId);

        $this['personAddParentPartnerFemaleForm-femaleId']->setItems($persons);

        $this->presenter->template->modalName = 'personAddParentFemalePartner';

        $this->presenter->payload->showModal = true;

        $this->presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddParentPartnerFemaleForm()
    {
        $formFactory = new RelationForm($this->translator);

        $form = $formFactory->create();
        $form->addHidden('_maleId');
        $form->onAnchor[] = [$this, 'personAddParentPartnerFemaleFormAnchor'];
        $form->onValidate[] = [$this, 'personAddParentPartnerFemaleFormValidate'];
        $form->onSuccess[] = [$this, 'personAddParentPartnerFemaleFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddParentPartnerFemaleFormAnchor()
    {
        $this->presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddParentPartnerFemaleFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairsCached($this->translator);

        $maleHiddenControl = $form->getComponent('_maleId');

        $maleControl = $form->getComponent('maleId');
        $maleControl->setItems($persons);
        $maleControl->setValue($maleHiddenControl->getValue())
            ->validate();

        $femaleControl = $form->getComponent('femaleId');
        $femaleControl->setItems($persons)
            ->validate();

        $form->removeComponent($maleHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddParentPartnerFemaleFormSuccess(Form $form, ArrayHash $values)
    {
        $this->relationManager->add($values);

        $person = $this->personFacade->getByPrimaryKeyCached($this->getParameter('id'));

        $this->prepareParentsRelations($person->father, $person->mother);

        $this->presenter->payload->showModal = false;

        $this->presenter->flashMessage('relation_added', BasePresenter::FLASH_SUCCESS);

        $this->presenter->redrawControl('flashes');
        $this->presenter->redrawControl('father_relations');
        $this->presenter->redrawControl('mother_relations');
    }
}