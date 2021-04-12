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
use Rendix2\FamilyTree\App\Controls\Forms\RelationForm;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Managers\RelationManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Services\PersonUpdateService;

/**
 * Class PersonAddPartnerFemaleModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddPartnerFemaleModal extends Control
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
     * PersonAddPartnerFemaleModal constructor.
     *
     * @param PersonManager       $personManager
     * @param PersonUpdateService $personUpdateService
     * @param RelationForm        $relationForm
     * @param RelationManager     $relationContainer
     * @param ITranslator         $translator
     */
    public function __construct(
        PersonManager $personManager,
        PersonUpdateService $personUpdateService,
        RelationForm $relationForm,
        RelationManager $relationContainer,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->relationForm = $relationForm;

        $this->personManager = $personManager;
        $this->personUpdateService = $personUpdateService;
        $this->relationManager = $relationContainer;
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

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $persons = $this->personManager->select()->getSettingsCachedManager()->getAllPairs();
        $females = $this->personManager->select()->getSettingsCachedManager()->getFemalesPairs();

        $this['personAddPartnerFemaleForm-_maleId']->setDefaultValue($personId);
        $this['personAddPartnerFemaleForm-maleId']->setItems($persons)
            ->setDisabled()
            ->setDefaultValue($personId);

        $this['personAddPartnerFemaleForm-femaleId']->setItems($females);

        $presenter->template->modalName = 'personAddPartnerFemale';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddPartnerFemaleForm()
    {
        $form = $this->relationForm->create();

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
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddPartnerFemaleFormValidate(Form $form)
    {
        $females = $this->personManager->select()->getSettingsCachedManager()->getFemalesPairs();
        $persons = $this->personManager->select()->getSettingsCachedManager()->getAllPairs();

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

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->relationManager->insert()->insert((array) $values);

        $this->personUpdateService->prepareRelations($presenter, $values->maleId);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('relation_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('relation_females');
    }
}
