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
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\SourceTypeForm;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\SourceManager;
use Rendix2\FamilyTree\App\Managers\SourceTypeManager;
use Rendix2\FamilyTree\App\Model\Facades\SourceFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\SourceType\SourceTypeEditDeleteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\SourceType\SourceTypeListDeleteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\SourceType\SourceTypeDeleteSourceModal;

/**
 * Class SourceTypePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class SourceTypePresenter extends BasePresenter
{
    use SourceTypeEditDeleteModal;
    use SourceTypeListDeleteModal;

    use SourceTypeDeleteSourceModal;

    /**
     * @var SourceFacade $sourceFacade
     */
    private $sourceFacade;

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
     * SourceTypePresenter constructor.
     *
     * @param SourceFacade $sourceFacade
     * @param SourceManager $sourceManager
     * @param SourceTypeManager $sourceTypeManager
     */
    public function __construct(
        SourceFacade $sourceFacade,
        SourceManager $sourceManager,
        SourceTypeManager $sourceTypeManager
    ) {
        parent::__construct();

        $this->sourceFacade = $sourceFacade;
        $this->sourceManager = $sourceManager;
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
            $sourceType = $this->sourceTypeManager->getByPrimaryKey($id);

            if (!$sourceType) {
                $this->error('Item not found.');
            }

            $this['form']->setDefaults((array)$sourceType);
        }
    }

    /**
     * @param int|null $id
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $sources = [];
        } else {
            $sources = $this->sourceFacade->getBySourceTypeCached($id);
        }

        $this->template->sources = $sources;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
    }

    /**
     * @return Form
     */
    protected function createComponentForm()
    {
        $formFactory = new SourceTypeForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'saveForm'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveForm(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->sourceTypeManager->updateByPrimaryKey($id, $values);
            $this->flashMessage('item_updated', self::FLASH_SUCCESS);
        } else {
            $id = $this->sourceTypeManager->add($values);
            $this->flashMessage('item_added', self::FLASH_SUCCESS);
        }

        $this->redirect(':edit', $id);
    }
}
