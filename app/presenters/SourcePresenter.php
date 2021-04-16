<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourcePresenter.php
 * User: Tomáš Babický
 * Date: 01.10.2020
 * Time: 23:45
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\SourceForm;
use Rendix2\FamilyTree\App\Controls\Modals\Source\Container\SourceModalContainer;
use Rendix2\FamilyTree\App\Model\Facades\SourceFacade;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Managers\SourceManager;
use Rendix2\FamilyTree\App\Model\Managers\SourceTypeManager;

/**
 * Class SourcePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class SourcePresenter extends BasePresenter
{
    /**
     * @var PersonManager $personManager
     */
    private $personManager;

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
     * @var SourceModalContainer $sourceModalContainer
     */
    private $sourceModalContainer;

    /**
     * SourcePresenter constructor.
     *
     * @param SourceModalContainer $sourceModalContainer
     * @param PersonManager        $personManager
     * @param SourceFacade         $sourceFacade
     * @param SourceForm           $sourceForm
     * @param SourceManager        $sourceContainer
     * @param SourceTypeManager    $sourceTypeManager
     */
    public function __construct(
        SourceModalContainer $sourceModalContainer,
        PersonManager $personManager,
        SourceFacade $sourceFacade,
        SourceForm $sourceForm,
        SourceManager $sourceContainer,
        SourceTypeManager $sourceTypeManager
    ) {
        parent::__construct();

        $this->sourceModalContainer = $sourceModalContainer;

        $this->sourceFacade = $sourceFacade;
        $this->sourceForm = $sourceForm;

        $this->sourceManager = $sourceContainer;
        $this->sourceTypeManager = $sourceTypeManager;

        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $sources = $this->sourceFacade->select()->getCachedManager()->getAll();

        $this->template->sources = $sources;
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $persons = $this->personManager->select()->getSettingsCachedManager()->getAllPairs();
        $sourceTypes = $this->sourceTypeManager->select()->getCachedManager()->getPairs('name');

        $this['sourceForm-personId']->setItems($persons);
        $this['sourceForm-sourceTypeId']->setItems($sourceTypes);

        if ($id !== null) {
            $source = $this->sourceFacade->select()->getCachedManager()->getByPrimaryKey($id);

            if (!$source) {
                $this->error('Item not found.');
            }

            $this['sourceForm-personId']->setDefaultValue($source->person->id);
            $this['sourceForm-sourceTypeId']->setDefaultValue($source->sourceType->id);
            $this['sourceForm']->setDefaults((array) $source);
        }
    }

    /**
     * @param int|null $id
     */
    public function renderEdit($id = null)
    {
        $source = $this->sourceFacade->select()->getCachedManager()->getByPrimaryKey($id);

        $this->template->source = $source;
    }

    /**
     * @return Form
     */
    protected function createComponentSourceForm()
    {
        $form = $this->sourceForm->create();

        $form->onSuccess[] = [$this, 'sourceFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function sourceFormSuccess(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->sourceManager->update()->updateByPrimaryKey($id, $values);

            $this->flashMessage('source_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->sourceManager->insert()->insert((array) $values);

            $this->flashMessage('source_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Source:edit', $id);
    }

    public function createComponentSourceAddSourceTypeModal()
    {
        return $this->sourceModalContainer->getSourceAddSourceTypeModalFactory()->create();
    }

    public function createComponentSourceDeleteSourceFromEditModal()
    {
        return $this->sourceModalContainer->getSourceDeleteSourceFromEditModalFactory()->create();
    }

    public function createComponentSourceDeleteSourceFromListModal()
    {
        return $this->sourceModalContainer->getSourceDeleteSourceFromListModalFactory()->create();
    }
}
