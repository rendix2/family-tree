<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameAddGenusModal.php
 * User: Tomáš Babický
 * Date: 04.12.2020
 * Time: 2:14
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Name;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\GenusForm;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class NameAddGenusModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Name
 */
class NameAddGenusModal extends Control
{
    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * NameAddGenusModal constructor.
     *
     * @param GenusManager $genusManager
     * @param ITranslator $translator
     */
    public function __construct(
        GenusManager $genusManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->genusManager = $genusManager;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['nameAddGenusForm']->render();
    }

    /**
     * @return void
     */
    public function handleNameAddGenus()
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Name:edit', $presenter->getParameter('id'));
        }

        $presenter->template->modalName = 'nameAddGenus';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentNameAddGenusForm()
    {
        $formFactory = new GenusForm($this->translator);

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'nameAddGenusFormAnchor'];
        $form->onValidate[] = [$this, 'nameAddGenusFormValidate'];
        $form->onSuccess[] = [$this, 'nameAddGenusFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function nameAddGenusFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function nameAddGenusFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function nameAddGenusFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Name:edit', $presenter->getParameter('id'));
        }

        $this->genusManager->add($values);

        $genuses = $this->genusManager->getPairsCached('surname');

        $presenter['nameForm-genusId']->setItems($genuses);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('genus_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('jsFormCallback');
        $presenter->redrawControl('flashes');
        $presenter->redrawControl('nameFormWrapper');
    }
}
