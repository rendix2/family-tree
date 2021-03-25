<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteNameModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:12
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Model\Facades\NameFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonDeleteNameModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeletePersonNameModal extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var NameManager $nameManager
     */
    private $nameManager;

    /**
     * @var NameFacade $nameFacade
     */
    private $nameFacade;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var NameFilter $nameFilter
     */
    private $nameFilter;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * PersonDeletePersonNameModal constructor.
     *
     * @param ITranslator $translator
     * @param NameManager $nameManager
     * @param NameFacade $nameFacade
     * @param PersonFacade $personFacade
     * @param NameFilter $nameFilter
     * @param PersonFilter $personFilter
     */
    public function __construct(
        ITranslator $translator,
        NameManager $nameManager,
        NameFacade $nameFacade,
        PersonFacade $personFacade,
        NameFilter $nameFilter,
        PersonFilter $personFilter
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->nameManager = $nameManager;
        $this->nameFacade = $nameFacade;
        $this->personFacade = $personFacade;
        $this->nameFilter = $nameFilter;
        $this->personFilter = $personFilter;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteNameForm']->render();
    }

    /**
     * @param int $personId
     * @param int $nameId
     */
    public function handlePersonDeletePersonName($personId, $nameId)
    {
        $presenter = $this->presenter;

        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        if ($this->presenter->isAjax()) {
            $this['personDeleteNameForm']->setDefaults(
                [
                    'nameId' => $nameId,
                    'personId' => $personId
                ]
            );

            $personFilter = $this->personFilter;
            $nameFilter = $this->nameFilter;

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
            $nameModalItem = $this->nameFacade->getByPrimaryKeyCached($nameId);

            $this->presenter->template->modalName = 'personDeleteName';
            $this->presenter->template->personModalItem = $personFilter($personModalItem);
            $this->presenter->template->nameModalItem = $nameFilter($nameModalItem);

            $this->presenter->payload->showModal = true;

            $this->presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteNameForm()
    {
        $formFactory = new DeleteModalForm($this->translator);
        $form = $formFactory->create([$this, 'personDeleteNameFormYesOnClick']);

        $form->addHidden('personId');
        $form->addHidden('nameId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteNameFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if ($this->presenter->isAjax()) {
            $this->nameManager->deleteByPrimaryKey($values->nameId);

            $names = $this->nameManager->getByPersonId($values->personId);

            $this->presenter->template->names = $names;

            $this->presenter->payload->showModal = false;

            $this->presenter->flashMessage('name_deleted', BasePresenter::FLASH_SUCCESS);

            $this->presenter->redrawControl('flashes');
            $this->presenter->redrawControl('names');
        } else {
            $this->presenter->redirect('Person:edit', $values->personId);
        }
    }
}
