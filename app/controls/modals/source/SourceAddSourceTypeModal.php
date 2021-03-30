<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceAddSourceTypeModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:39
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Source;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;

use Rendix2\FamilyTree\App\Managers\SourceTypeManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class SourceAddSourceTypeModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Source
 */
class SourceAddSourceTypeModal extends Control
{
    /**
     * @var SourceTypeManager $sourceTypeManager
     */
    private $sourceTypeManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * SourceAddSourceTypeModal constructor.
     *
     * @param SourceTypeManager $sourceTypeManager
     * @param ITranslator $translator
     */
    public function __construct(
        SourceTypeManager $sourceTypeManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->sourceTypeManager = $sourceTypeManager;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['sourceAddSourceTypeForm']->render();
    }

    /**
     * @return void
     */
    public function handleSourceAddSourceType()
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Source:edit', $presenter->getParameter('id'));
        }

        $presenter->template->modalName = 'sourceAddSourceType';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentSourceAddSourceTypeForm()
    {
        $formFactory = new SourceTypeForm($this->translator);

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'sourceAddSourceTypeFormAnchor'];
        $form->onValidate[] = [$this, 'sourceAddSourceTypeFormValidate'];
        $form->onSuccess[] = [$this, 'sourceAddSourceTypeFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function sourceAddSourceTypeFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function sourceAddSourceTypeFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function sourceAddSourceTypeFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Source:edit', $presenter->getParameter('id'));
        }

        $this->sourceTypeManager->add($values);

        $sourceTypes = $this->sourceTypeManager->getPairsCached('name');

        $presenter['sourceForm-sourceTypeId']->setItems($sourceTypes);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('source_type_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('sourceFormWrapper');
        $presenter->redrawControl('jsFormCallback');
    }
}
