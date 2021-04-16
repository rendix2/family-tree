<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeAddSourceModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:32
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\SourceType;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\SourceForm;
use Rendix2\FamilyTree\App\Model\Facades\SourceFacade;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Managers\SourceManager;
use Rendix2\FamilyTree\App\Model\Managers\SourceTypeManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class SourceTypeAddSourceModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\SourceType
 */
class SourceTypeAddSourceModal extends Control
{
    /**
     * @var SourceFacade $sourceFacade
     */
    private $sourceFacade;

    /**
     * @var SourceForm $sourceForm
     */
    private $sourceForm;

    /**
     * @var SourceManager $sourceManager
     */
    private $sourceManager;

    /**
     * @var SourceTypeManager $sourceTypeManager
     */
    private $sourceTypeManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * SourceTypeAddSourceModal constructor.
     *
     * @param SourceFacade      $sourceFacade
     * @param SourceManager     $sourceContainer
     * @param SourceForm        $sourceForm
     * @param SourceTypeManager $sourceTypeManager
     * @param PersonManager     $personManager
     */
    public function __construct(
        SourceFacade $sourceFacade,
        SourceManager $sourceContainer,
        SourceForm $sourceForm,
        SourceTypeManager $sourceTypeManager,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->sourceFacade = $sourceFacade;
        $this->sourceForm = $sourceForm;
        $this->sourceManager = $sourceContainer;
        $this->sourceTypeManager = $sourceTypeManager;
        $this->personManager = $personManager;
    }

    public function render()
    {
        $this['sourceTypeAddSourceForm']->render();
    }

    /**
     * @param int $sourceTypeId
     *
     * @return void
     */
    public function handleSourceTypeAddSource($sourceTypeId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('SourceType:edit', $presenter->getParameter('id'));
        }

        $persons = $this->personManager->select()->getSettingsCachedManager()->getAllPairs();
        $sourceTypes = $this->sourceTypeManager->select()->getCachedManager()->getPairs('name');

        $this['sourceTypeAddSourceForm-personId']->setItems($persons);

        $this['sourceTypeAddSourceForm-_sourceTypeId']->setDefaultValue($sourceTypeId);
        $this['sourceTypeAddSourceForm-sourceTypeId']->setItems($sourceTypes)
            ->setDisabled()
            ->setDefaultValue($sourceTypeId);

        $presenter->template->modalName = 'sourceTypeAddSource';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentSourceTypeAddSourceForm()
    {
        $form = $this->sourceForm->create();

        $form->addHidden('_sourceTypeId');

        $form->onAnchor[] = [$this, 'sourceTypeAddSourceFormAnchor'];
        $form->onValidate[] = [$this, 'sourceTypeAddSourceFormValidate'];
        $form->onSuccess[] = [$this, 'sourceTypeAddSourceFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function sourceTypeAddSourceFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function sourceTypeAddSourceFormValidate(Form $form)
    {
        $persons = $this->personManager->select()->getCachedManager()->getAllPairs();

        $personControl = $form->getComponent('personId');

        $personControl->setItems($persons);
        $personControl->validate();

        $sourceTypes = $this->sourceTypeManager->select()->getCachedManager()->getPairs('name');

        $sourceTypeHiddenControl = $form->getComponent('_sourceTypeId');

        $sourceTypeControl = $form->getComponent('sourceTypeId');
        $sourceTypeControl->setItems($sourceTypes)
            ->setValue($sourceTypeHiddenControl->getValue())
            ->validate();

        $form->removeComponent($sourceTypeHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function sourceTypeAddSourceFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('SourceType:edit', $presenter->getParameter('id'));
        }

        $this->sourceManager->insert()->insert((array) $values);

        $sources = $this->sourceFacade->select()->getCachedManager()->getBySourceTypeId($values->sourceTypeId);

        $presenter->template->souces = $sources;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('source_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('sources');
    }
}
