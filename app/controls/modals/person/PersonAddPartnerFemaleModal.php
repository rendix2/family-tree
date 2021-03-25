<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPartnerFemaleModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:00
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\RelationForm;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Managers\RelationManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonPrepareMethods;

/**
 * Class PersonAddPartnerFemaleModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddPartnerFemaleModal extends Control
{
    use PersonPrepareMethods;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

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
     * PersonAddPartnerFemaleModal constructor.
     *
     * @param PersonManager $personManager
     * @param PersonSettingsManager $personSettingsManager
     * @param RelationManager $relationManager
     * @param ITranslator $translator
     */
    public function __construct(
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager,
        RelationManager $relationManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->personManager = $personManager;
        $this->personSettingsManager = $personSettingsManager;
        $this->relationManager = $relationManager;
        $this->translator = $translator;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddPartnerFemaleForm']->render();
    }

    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddPartnerFemale($personId)
    {
        $presenter = $this->presenter;

        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);
        $females = $this->personSettingsManager->getFemalesPairsCached($this->translator);

        $this['personAddPartnerFemaleForm-_maleId']->setDefaultValue($personId);
        $this['personAddPartnerFemaleForm-maleId']->setItems($persons)
            ->setDisabled()
            ->setDefaultValue($personId);

        $this['personAddPartnerFemaleForm-femaleId']->setItems($females);

        $this->presenter->template->modalName = 'personAddPartnerFemale';

        $this->presenter->payload->showModal = true;

        $this->presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddPartnerFemaleForm()
    {
        $formFactory = new RelationForm($this->translator);

        $form = $formFactory->create();
        $form->addHidden('_maleId');
        $form->onAnchor[] = [$this, 'personAddPartnerFemaleFormAnchor'];
        $form->onValidate[] = [$this, 'personAddPartnerFemaleFormValidate'];
        $form->onSuccess[] = [$this, 'personAddPartnerFemaleFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddPartnerFemaleFormAnchor()
    {
        $this->presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddPartnerFemaleFormValidate(Form $form)
    {
        $females = $this->personManager->getFemalesPairsCached($this->translator);
        $persons = $this->personManager->getAllPairsCached($this->translator);

        $maleHiddenControl = $form->getComponent('_maleId');

        $maleControl = $form->getComponent('maleId');
        $maleControl->setItems($persons);
        $maleControl->setValue($maleHiddenControl->getValue())
            ->validate();

        $femaleControl = $form->getComponent('femaleId');
        $femaleControl->setItems($females)
            ->validate();

        $form->removeComponent($maleHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddPartnerFemaleFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        $this->relationManager->add($values);

        $this->prepareRelations($values->maleId);

        $this->presenter->payload->showModal = false;

        $this->presenter->flashMessage('relation_added', BasePresenter::FLASH_SUCCESS);

        $this->presenter->redrawControl('flashes');
        $this->presenter->redrawControl('relation_females');
    }
}
