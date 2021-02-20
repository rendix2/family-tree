<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteSisterModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:16
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonPrepareMethods;

/**
 * Class PersonDeleteSisterModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteSisterModal extends Control
{
    use PersonPrepareMethods;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonFilter
     */
    private $personFilter;

    /**
     * PersonDeleteSisterModal constructor.
     *
     * @param ITranslator $translator
     * @param PersonFacade $personFacade
     * @param PersonManager $personManager
     * @param PersonFilter $personFilter
     */
    public function __construct(
        ITranslator $translator,
        PersonFacade $personFacade,
        PersonManager $personManager,
        PersonFilter $personFilter
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
        $this->personFilter = $personFilter;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteSisterForm']->render();
    }

    /**
     * @param int $personId
     * @param int $sisterId
     */
    public function handlePersonDeleteSister($personId, $sisterId)
    {
        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        $this['personDeleteSisterForm']->setDefaults(
            [
                'personId' => $personId,
                'sisterId' => $sisterId
            ]
        );

        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
        $sisterModalItem = $this->personManager->getByPrimaryKeyCached($sisterId);

        $this->presenter->template->modalName = 'personDeleteSister';
        $this->presenter->template->personModalItem = $personFilter($personModalItem);
        $this->presenter->template->sisterModalItem = $personFilter($sisterModalItem);

        $this->presenter->payload->showModal = true;

        $this->presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteSisterForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'personDeleteSisterFormYesOnClick']);
        $form->addHidden('personId');
        $form->addHidden('sisterId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteSisterFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->presenter->isAjax()) {
            $this->personManager->updateByPrimaryKey($values->sisterId,
                [
                    'fatherId' => null,
                    'motherId' => null
                ]
            );

            $sister = $this->personFacade->getByPrimaryKeyCached($values->sisterId);

            $this->prepareBrothersAndSisters($values->sisterId, $sister->father, $sister->mother);

            $this->presenter->payload->showModal = false;

            $this->presenter->flashMessage('person_sister_deleted', BasePresenter::FLASH_SUCCESS);

            $this->presenter->redrawControl('sisters');
            $this->presenter->redrawControl('flashes');
        } else {
            $this->presenter->redirect('Person:edit', $values->personId);
        }
    }
}