<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddSonModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:06
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\PersonSelectForm;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\PersonFilter;

use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonAddSonModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddSonModal extends Control
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
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonSelectForm $personSelectForm
     */
    private $personSelectForm;

    /**
     * PersonAddSonModal constructor.
     *
     * @param ITranslator           $translator
     * @param PersonSettingsManager $personSettingsManager
     * @param PersonFilter          $personFilter
     * @param PersonFacade          $personFacade
     * @param PersonManager         $personManager
     * @param PersonSelectForm      $personSelectForm
     */
    public function __construct(
        ITranslator $translator,
        PersonSettingsManager $personSettingsManager,
        PersonFilter $personFilter,
        PersonFacade $personFacade,
        PersonManager $personManager,
        PersonSelectForm $personSelectForm
    ) {
        parent::__construct();

        $this->personSelectForm = $personSelectForm;

        $this->translator = $translator;
        $this->personSettingsManager = $personSettingsManager;
        $this->personFilter = $personFilter;
        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddSonForm']->render();
    }

    /**
     * @param int $personId
     */
    public function handlePersonAddSon($personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getMalesPairs();

        $this['personAddSonForm-selectedPersonId']->setItems($persons);
        $this['personAddSonForm']->setDefaults(['personId' => $personId,]);

        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

        $presenter->template->modalName = 'personAddSon';
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddSonForm()
    {
        $form = $this->personSelectForm->create();

        $form->onSuccess[] = [$this, 'personAddSonFormSuccess'];
        $form->onAnchor[] = [$this, 'personAddSonFormAnchor'];
        $form->onValidate[] = [$this, 'personAddSonFormValidate'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @param Form $form
     *
     * @return void
     */
    public function personAddSonFormAnchor(Form $form)
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddSonFormValidate(Form $form, ArrayHash $values)
    {
        $persons = $this->personManager->getMalesPairs();

        $component = $form->getComponent('selectedPersonId');
        $component->setItems($persons)
            ->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddSonFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $formData = $form->getHttpData();
        $personId = $values->personId;
        $selectedPersonId = $formData['selectedPersonId'];

        $person = $this->personFacade->getByPrimaryKeyCached($personId);

        if ($person->gender === 'm') {
            $this->personManager->updateByPrimaryKey($selectedPersonId, ['fatherId' => $personId]);
        } else {
            $this->personManager->updateByPrimaryKey($selectedPersonId, ['motherId' => $personId]);
        }

        $sons = $this->personSettingsManager->getSonsByPersonCached($person);

        $presenter->template->sons = $sons;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_son_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('sons');
    }
}
