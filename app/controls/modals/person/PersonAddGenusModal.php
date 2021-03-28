<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddGenusModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:57
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\GenusForm;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonAddGenusModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddGenusModal extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * PersonAddGenusModal constructor.
     *
     * @param ITranslator $translator
     * @param GenusManager $genusManager
     */
    public function __construct(
        ITranslator $translator,
        GenusManager $genusManager
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->genusManager = $genusManager;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddGenusForm']->render();
    }

    /**
     * @return void
     */
    public function handlePersonAddGenus()
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $presenter->template->modalName = 'personAddGenus';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddGenusForm()
    {
        $formFactory = new GenusForm($this->translator);

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'personAddGenusFormAnchor'];
        $form->onValidate[] = [$this, 'personAddGenusFormValidate'];
        $form->onSuccess[] = [$this, 'personAddGenusFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddGenusFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddGenusFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddGenusFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        $this->genusManager->add($values);

        $genuses = $this->genusManager->getPairs('surname');

        $presenter['personForm-genusId']->setItems($genuses);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('genus_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('personFormWrapper');
        $presenter->redrawControl('ks');
    }
}
