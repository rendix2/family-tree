<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPersonNameModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:03
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\NameForm;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\NameFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonAddPersonNameModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddPersonNameModal extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var NameManager $nameManager
     */
    private $nameManager;

    /**
     * @var NameFacade $nameFacade
     */
    private $nameFacade;

    /**
     * PersonAddPersonNameModal constructor.
     *
     * @param ITranslator $translator
     * @param PersonSettingsManager $personSettingsManager
     * @param GenusManager $genusManager
     * @param PersonManager $personManager
     * @param NameManager $nameManager
     * @param NameFacade $nameFacade
     */
    public function __construct(
        ITranslator $translator,
        PersonSettingsManager $personSettingsManager,
        GenusManager $genusManager,
        PersonManager $personManager,
        NameManager $nameManager,
        NameFacade $nameFacade
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->personSettingsManager = $personSettingsManager;
        $this->genusManager = $genusManager;
        $this->personManager = $personManager;
        $this->nameManager = $nameManager;
        $this->nameFacade = $nameFacade;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddPersonNameForm']->render();
    }

    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddPersonName($personId)
    {
        $presenter = $this->presenter;

        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getAllPairs($this->translator);
        $genuses = $this->genusManager->getPairsCached('surname');

        $this['personAddPersonNameForm-personId']->setItems($persons)->setDisabled()->setDefaultValue($personId);
        $this['personAddPersonNameForm-_personId']->setDefaultValue($personId);
        $this['personAddPersonNameForm-genusId']->setItems($genuses);

        $this->presenter->template->modalName = 'personAddPersonName';

        $this->presenter->payload->showModal = true;

        $this->presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddPersonNameForm()
    {
        $formFactory = new NameForm($this->translator);

        $form = $formFactory->create();
        $form->addHidden('_personId');
        $form->onAnchor[] = [$this, 'personAddPersonNameFormAnchor'];
        $form->onValidate[] = [$this, 'personAddPersonNameFormValidate'];
        $form->onSuccess[] = [$this, 'personAddPersonNameFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddPersonNameFormAnchor()
    {
        $presenter = $this->presenter;

        $this->presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddPersonNameFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairs($this->translator);

        $personHiddenControl = $form->getComponent('_personId');

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons)
            ->setValue($personHiddenControl->getValue())
            ->validate();

        $genuses = $this->genusManager->getPairsCached('surname');

        $genusControl = $form->getComponent('genusId');
        $genusControl->setItems($genuses)
            ->validate();

        $form->removeComponent($personHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddPersonNameFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        $this->nameManager->add($values);

        $names = $this->nameFacade->getByPersonCached($values->personId);

        $this->presenter->template->names = $names;

        $this->presenter->payload->showModal = false;

        $this->presenter->flashMessage('name_added', BasePresenter::FLASH_SUCCESS);

        $this->presenter->redrawControl('names');
        $this->presenter->redrawControl('flashes');
    }
}
