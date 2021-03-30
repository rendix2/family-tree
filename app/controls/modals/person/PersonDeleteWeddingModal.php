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

use Nette\Application\IPresenter;
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
use Rendix2\FamilyTree\App\Services\PersonUpdateService;

/**
 * Class PersonDeleteWeddingModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteWeddingModal  extends Control
{
    /**
     * @var PersonUpdateService $personUpdateService
     */
    private $personUpdateService;

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
     * @param PersonUpdateService $personUpdateService
     * @param ITranslator $translator
     * @param WeddingManager $weddingManager
     * @param WeddingFacade $weddingFacade
     * @param WeddingFilter $weddingFilter
     */
    public function __construct(
        PersonUpdateService $personUpdateService,
        ITranslator $translator,
        WeddingManager $weddingManager,
        WeddingFacade $weddingFacade,
        WeddingFilter $weddingFilter
    ) {
        parent::__construct();

        $this->personUpdateService = $personUpdateService;
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

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this['personDeleteWeddingForm']->setDefaults(
            [
                'weddingId' => $weddingId,
                'personId' => $personId
            ]
        );

        $weddingFilter = $this->weddingFilter;

        $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);

        $presenter->template->modalName = 'personDeleteWedding';
        $presenter->template->weddingModalItem = $weddingFilter($weddingModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
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

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->weddingManager->deleteByPrimaryKey($values->weddingId);

        $this->personUpdateService->prepareWeddings($presenter, $values->personId);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('wedding_deleted', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('husbands');
        $presenter->redrawControl('wives');
    }
}