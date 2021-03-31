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
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;

use Rendix2\FamilyTree\App\Controls\Forms\SourceForm;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Managers\SourceManager;
use Rendix2\FamilyTree\App\Managers\SourceTypeManager;
use Rendix2\FamilyTree\App\Model\Facades\SourceFacade;
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
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * SourceTypeAddSourceModal constructor.
     *
     * @param SourceFacade $sourceFacade
     * @param SourceManager $sourceManager
     * @param SourceTypeManager $sourceTypeManager
     * @param PersonManager $personManager
     * @param PersonSettingsManager $personSettingsManager
     * @param ITranslator $translator
     */
    public function __construct(
        SourceFacade $sourceFacade,
        SourceManager $sourceManager,
        SourceForm $sourceForm,
        SourceTypeManager $sourceTypeManager,
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->sourceFacade = $sourceFacade;
        $this->sourceForm = $sourceForm;
        $this->sourceManager = $sourceManager;
        $this->sourceTypeManager = $sourceTypeManager;
        $this->personManager = $personManager;
        $this->personSettingsManager = $personSettingsManager;
        $this->translator = $translator;
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

        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);
        $sourceTypes = $this->sourceTypeManager->getPairsCached('name');

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
        $persons = $this->personManager->getAllPairsCached($this->translator);

        $personControl = $form->getComponent('personId');

        $personControl->setItems($persons);
        $personControl->validate();

        $sourceTypes = $this->sourceTypeManager->getPairsCached('name');

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

        $this->sourceManager->add($values);

        $sources = $this->sourceFacade->getBySourceTypeCached($values->sourceTypeId);

        $presenter->template->souces = $sources;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('source_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('sources');
    }
}
