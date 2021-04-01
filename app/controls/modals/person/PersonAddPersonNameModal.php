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

use Rendix2\FamilyTree\App\Controls\Forms\NameForm;
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
     * @var NameForm $nameForm
     */
    private $nameForm;

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
     * @param NameForm $nameForm
     * @param NameManager $nameManager
     * @param NameFacade $nameFacade
     */
    public function __construct(
        ITranslator $translator,
        PersonSettingsManager $personSettingsManager,
        GenusManager $genusManager,
        PersonManager $personManager,
        NameForm $nameForm,
        NameManager $nameManager,
        NameFacade $nameFacade
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->personSettingsManager = $personSettingsManager;
        $this->genusManager = $genusManager;
        $this->personManager = $personManager;
        $this->nameForm = $nameForm;
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

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getAllPairs();
        $genuses = $this->genusManager->getPairsCached('surname');

        $this['personAddPersonNameForm-personId']->setItems($persons)->setDisabled()->setDefaultValue($personId);
        $this['personAddPersonNameForm-_personId']->setDefaultValue($personId);
        $this['personAddPersonNameForm-genusId']->setItems($genuses);

        $presenter->template->modalName = 'personAddPersonName';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddPersonNameForm()
    {
        $form = $this->nameForm->create();

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

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddPersonNameFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairs();

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

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->nameManager->add($values);

        $names = $this->nameFacade->getByPersonIdCached($values->personId);

        $presenter->template->names = $names;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('name_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('names');
        $presenter->redrawControl('flashes');
    }
}
