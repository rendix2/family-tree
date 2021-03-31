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
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;

use Rendix2\FamilyTree\App\Controls\Forms\RelationForm;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Managers\RelationManager;
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
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var PersonUpdateService $personUpdateService
     */
    private $personUpdateService;

    /**
     * @var ITranslator $translator
     */
    private $translator;

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
     * @param PersonManager $personManager
     * @param PersonSettingsManager $personSettingsManager
     * @param PersonUpdateService $personUpdateService
     * @param ITranslator $translator
     * @param RelationManager $relationManager
     */
    public function __construct(
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager,
        PersonUpdateService $personUpdateService,
        ITranslator $translator,
        RelationForm $relationForm,
        RelationManager $relationManager
    ) {
        parent::__construct();

        $this->relationForm = $relationForm;

        $this->personManager = $personManager;
        $this->personSettingsManager = $personSettingsManager;
        $this->personUpdateService = $personUpdateService;
        $this->translator = $translator;
        $this->relationManager = $relationManager;
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

        $males = $this->personSettingsManager->getMalesPairsCached($this->translator);
        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);

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
        $males = $this->personManager->getMalesPairsCached($this->translator);
        $persons = $this->personManager->getAllPairsCached($this->translator);

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

        $this->relationManager->add($values);

        $this->personUpdateService->prepareRelations($presenter, $values->femaleId);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('relation_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('relation_males');
        $presenter->redrawControl('flashes');
    }
}
