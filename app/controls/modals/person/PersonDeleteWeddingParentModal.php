<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteWeddingParentModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:17
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonPrepareMethods;

/**
 * Class PersonDeleteWeddingParentModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteWeddingParentModal extends Control
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
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * @var WeddingFilter $weddingFilter
     */
    private $weddingFilter;

    /**
     * PersonDeleteWeddingParentModal constructor.
     * @param ITranslator $translator
     * @param PersonFacade $personFacade
     * @param WeddingManager $weddingManager
     * @param WeddingFacade $weddingFacade
     * @param WeddingFilter $weddingFilter
     */
    public function __construct(
        ITranslator $translator,
        PersonFacade $personFacade,
        WeddingManager $weddingManager,
        WeddingFacade $weddingFacade,
        WeddingFilter $weddingFilter
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->personFacade = $personFacade;
        $this->weddingManager = $weddingManager;
        $this->weddingFacade = $weddingFacade;
        $this->weddingFilter = $weddingFilter;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteParentsWeddingForm']->render();
    }

    /**
     * @param int $personId
     * @param int $weddingId
     */
    public function handlePersonDeleteParentsWedding($personId, $weddingId)
    {
        $presenter = $this->presenter;

        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        $this['personDeleteParentsWeddingForm']->setDefaults(
            [
                'weddingId' => $weddingId,
                'personId' => $personId
            ]
        );

        $weddingFilter = $this->weddingFilter;

        $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);

        $this->presenter->template->modalName = 'personDeleteParentsWedding';
        $this->presenter->template->weddingModalItem = $weddingFilter($weddingModalItem);

        $this->presenter->payload->showModal = true;

        $this->presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteParentsWeddingForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'personDeleteParentsWeddingFormYesOnClick']);
        $form->addHidden('weddingId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteParentsWeddingFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if ($this->presenter->isAjax()) {
            $this->weddingManager->deleteByPrimaryKey($values->weddingId);

            $person = $this->personFacade->getByPrimaryKeyCached($values->personId);

            $this->prepareParentsWeddings($person->father, $person->mother);

            $this->presenter->payload->showModal = false;

            $this->presenter->flashMessage('wedding_deleted', BasePresenter::FLASH_SUCCESS);

            $this->presenter->redrawControl('flashes');
            $this->presenter->redrawControl('father_wives');
            $this->presenter->redrawControl('mother_husbands');
        } else {
            $this->presenter->redirect('Person:edit', $values->personId);
        }
    }
}
