<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteWeddingModal.php
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
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonPrepareMethods;

/**
 * Class PersonDeleteWeddingModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteWeddingModal  extends Control
{
    use PersonPrepareMethods;

    /**
     * @var ITranslator $translator
     */
    private $translator;

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
     * PersonDeleteWeddingModal constructor.
     *
     * @param ITranslator $translator
     * @param WeddingManager $weddingManager
     * @param WeddingFacade $weddingFacade
     * @param WeddingFilter $weddingFilter
     */
    public function __construct(
        ITranslator $translator,
        WeddingManager $weddingManager,
        WeddingFacade $weddingFacade,
        WeddingFilter $weddingFilter
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->weddingManager = $weddingManager;
        $this->weddingFacade = $weddingFacade;
        $this->weddingFilter = $weddingFilter;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteWeddingForm']->render();
    }

    /**
     * @param int $personId
     * @param int $weddingId
     */
    public function handlePersonDeleteWedding($personId, $weddingId)
    {
        $presenter = $this->presenter;

        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        $this['personDeleteWeddingForm']->setDefaults(
            [
                'weddingId' => $weddingId,
                'personId' => $personId
            ]
        );

        $weddingFilter = $this->weddingFilter;

        $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);

        $this->presenter->template->modalName = 'personDeleteWedding';
        $this->presenter->template->weddingModalItem = $weddingFilter($weddingModalItem);

        $this->presenter->payload->showModal = true;

        $this->presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteWeddingForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'personDeleteWeddingFormYesOnClick']);
        $form->addHidden('weddingId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteWeddingFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if ($this->presenter->isAjax()) {
            $this->weddingManager->deleteByPrimaryKey($values->weddingId);

            $this->prepareWeddings($values->personId);

            $this->presenter->payload->showModal = false;

            $this->presenter->flashMessage('wedding_deleted', BasePresenter::FLASH_SUCCESS);

            $this->presenter->redrawControl('flashes');
            $this->presenter->redrawControl('husbands');
            $this->presenter->redrawControl('wives');
        } else {
            $this->presenter->redirect('Person:edit', $values->personId);
        }
    }
}