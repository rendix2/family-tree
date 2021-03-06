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
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\SourceTypeForm;
use Rendix2\FamilyTree\App\Model\Managers\SourceTypeManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class SourceAddSourceTypeModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Source
 */
class SourceAddSourceTypeModal extends Control
{
    /**
     * @var SourceTypeForm $sourceTypeForm
     */
    private $sourceTypeForm;

    /**
     * @var SourceTypeManager $sourceTypeManager
     */
    private $sourceTypeManager;

    /**
     * SourceAddSourceTypeModal constructor.
     *
     * @param SourceTypeForm    $sourceTypeForm
     * @param SourceTypeManager $sourceTypeManager
     */
    public function __construct(
        SourceTypeForm $sourceTypeForm,
        SourceTypeManager $sourceTypeManager
    ) {
        parent::__construct();

        $this->sourceTypeForm = $sourceTypeForm;
        $this->sourceTypeManager = $sourceTypeManager;
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
        $form = $this->sourceTypeForm->create();

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

        $this->sourceTypeManager->insert()->insert((array) $values);

        $sourceTypes = $this->sourceTypeManager->select()->getCachedManager()->getPairs('name');

        $presenter['sourceForm-sourceTypeId']->setItems($sourceTypes);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('source_type_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('sourceFormWrapper');
        $presenter->redrawControl('jsFormCallback');
    }
}
