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
use Rendix2\FamilyTree\App\Controls\Forms\RelationForm;
use Rendix2\FamilyTree\App\Facades\PersonFacade;

use Rendix2\FamilyTree\App\Facades\RelationFacade;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Managers\RelationManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Services\PersonUpdateService;

/**
 * Class PersonAddParentPartnerFemaleModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddParentPartnerFemaleModal extends Control
{
    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var RelationForm $relationForm
     */
    private $relationForm;

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
     * @var PersonUpdateService $personUpdateService
     */
    private $personUpdateService;

    /**
     * PersonAddParentPartnerFemaleModal constructor.
     *
     * @param PersonFacade $personFacade
     * @param RelationManager $relationManager
     * @param ITranslator $translator
     * @param PersonManager $personManager
     * @param PersonSettingsManager $personSettingsManager
     * @param PersonUpdateService $personUpdateService
     */
    public function __construct(
        PersonFacade $personFacade,
        RelationForm $relationForm,
        RelationManager $relationManager,
        ITranslator $translator,
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager,
        PersonUpdateService $personUpdateService
    ) {
        parent::__construct();

        $this->relationForm = $relationForm;

        $this->personFacade = $personFacade;
        $this->relationManager = $relationManager;
        $this->translator = $translator;
        $this->personManager = $personManager;
        $this->personSettingsManager = $personSettingsManager;
        $this->personUpdateService = $personUpdateService;
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
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);

        $this['personAddParentPartnerFemaleForm-_maleId']->setDefaultValue($personId);
        $this['personAddParentPartnerFemaleForm-maleId']->setItems($persons)
            ->setDisabled()
            ->setDefaultValue($personId);

        $this['personAddParentPartnerFemaleForm-femaleId']->setItems($persons);

        $presenter->template->modalName = 'personAddParentFemalePartner';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddParentPartnerFemaleForm()
    {
        $form = $this->relationForm->create();

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
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
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
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->relationManager->add($values);

        $person = $this->personFacade->getByPrimaryKeyCached($presenter->getParameter('id'));

        $this->personUpdateService->prepareParentsRelations($presenter, $person->father, $person->mother);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('relation_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('father_relations');
        $presenter->redrawControl('mother_relations');
    }
}
