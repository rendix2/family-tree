<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypePresenter.php
 * User: Tomáš Babický
 * Date: 01.10.2020
 * Time: 23:43
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Modals\SourceType\Container\SourceTypeModalContainer;
use Rendix2\FamilyTree\App\Forms\SourceTypeForm;
use Rendix2\FamilyTree\App\Managers\SourceTypeManager;
use Rendix2\FamilyTree\App\Model\Entities\SourceTypeEntity;
use Rendix2\FamilyTree\App\Model\Facades\SourceFacade;

/**
 * Class SourceTypePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class SourceTypePresenter extends BasePresenter
{
    /**
     * @var SourceFacade $sourceFacade
     */
    private $sourceFacade;

    /**
     * @var SourceTypeManager $sourceTypeManager
     */
    private $sourceTypeManager;

    /**
     * @var SourceTypeModalContainer $sourceTypeModalContainer
     */
    private $sourceTypeModalContainer;

    /**
     * SourceTypePresenter constructor.
     *
     * @param SourceTypeModalContainer $sourceTypeModalContainer
     * @param SourceFacade $sourceFacade
     * @param SourceTypeManager $sourceTypeManager
     */
    public function __construct(
        SourceTypeModalContainer $sourceTypeModalContainer,
        SourceFacade $sourceFacade,
        SourceTypeManager $sourceTypeManager
    ) {
        parent::__construct();

        $this->sourceTypeModalContainer = $sourceTypeModalContainer;

        $this->sourceFacade = $sourceFacade;

        $this->sourceTypeManager = $sourceTypeManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $sourceTypes = $this->sourceTypeManager->getAllCached();

        $this->template->sourceTypes = $sourceTypes;
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        if ($id !== null) {
            $sourceType = $this->sourceTypeManager->getByPrimaryKeyCached($id);

            if (!$sourceType) {
                $this->error('Item not found.');
            }

            $this['sourceTypeForm']->setDefaults((array) $sourceType);
        }
    }

    /**
     * @param int|null $id
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $sources = [];
            $sourceType = new SourceTypeEntity([]);
        } else {
            $sources = $this->sourceFacade->getBySourceTypeCached($id);
            $sourceType = $this->sourceTypeManager->getByPrimaryKeyCached($id);
        }

        $this->template->sourceType = $sourceType;
        $this->template->sources = $sources;
    }

    /**
     * @return Form
     */
    protected function createComponentSourceTypeForm()
    {
        $formFactory = new SourceTypeForm($this->translator);

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'sourceTypeSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function sourceTypeSuccess(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->sourceTypeManager->updateByPrimaryKey($id, $values);

            $this->flashMessage('source_type_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->sourceTypeManager->add($values);

            $this->flashMessage('source_type_added', self::FLASH_SUCCESS);
        }

        $this->redirect('SourceType:edit', $id);
    }

    public function createComponentSourceTypeDeleteSourceTypeFromEditModal()
    {
        return $this->sourceTypeModalContainer->getSourceTypeDeleteSourceTypeFromEditModalFactory()->create();
    }

    public function createComponentSourceTypeDeleteSourceTypeFromListModal()
    {
        return $this->sourceTypeModalContainer->getSourceTypeDeleteSourceTypeFromListModalFactory()->create();
    }

    public function createComponentSourceTypeAddSourceModal()
    {
        return $this->sourceTypeModalContainer->getSourceTypeAddSourceModalFactory()->create();
    }

    public function createComponentSourceTypeDeleteSourceModal()
    {
        return $this->sourceTypeModalContainer->getSourceTypeDeleteSourceModalFactory()->create();
    }
}
