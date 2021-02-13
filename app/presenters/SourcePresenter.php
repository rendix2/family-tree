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
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\SourceFilter;
use Rendix2\FamilyTree\App\Filters\SourceTypeFilter;
use Rendix2\FamilyTree\App\Forms\SourceForm;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Managers\SourceManager;
use Rendix2\FamilyTree\App\Managers\SourceTypeManager;
use Rendix2\FamilyTree\App\Model\Facades\SourceFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\Source\SourceAddSourceTypeModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Source\SourceDeleteSourceFromEditModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Source\SourceDeleteSourceFromListModal;

/**
 * Class SourcePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class SourcePresenter extends BasePresenter
{
    use SourceAddSourceTypeModal;

    use SourceDeleteSourceFromListModal;
    use SourceDeleteSourceFromEditModal;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;


    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

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
     * SourcePresenter constructor.
     *
     * @param PersonSettingsManager $personSettingsManager
     * @param SourceFacade $sourceFacade
     * @param SourceManager $sourceManager
     * @param SourceTypeManager $sourceTypeManager
     */
    public function __construct(
        PersonSettingsManager $personSettingsManager,
        SourceFacade $sourceFacade,
        SourceManager $sourceManager,
        SourceTypeManager $sourceTypeManager
    ) {
        parent::__construct();

        $this->personSettingsManager = $personSettingsManager;
        $this->sourceFacade = $sourceFacade;
        $this->sourceManager = $sourceManager;
        $this->sourceTypeManager = $sourceTypeManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $sources = $this->sourceFacade->getAllCached();

        $this->template->sources = $sources;

        $this->template->addFilter('person', new PersonFilter($this->translator, $this->getHttpRequest()));
        $this->template->addFilter('sourceType', new SourceTypeFilter());
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);
        $sourceTypes = $this->sourceTypeManager->getPairsCached('name');

        $this['sourceForm-personId']->setItems($persons);
        $this['sourceForm-sourceTypeId']->setItems($sourceTypes);

        if ($id !== null) {
            $source = $this->sourceFacade->getByPrimaryKeyCached($id);

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
        $source = $this->sourceFacade->getByPrimaryKeyCached($id);

        $this->template->source = $source;

        $this->template->addFilter('source', new SourceFilter());
    }

    /**
     * @return Form
     */
    protected function createComponentSourceForm()
    {
        $formFactory = new SourceForm($this->translator);

        $form = $formFactory->create();
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
            $this->sourceManager->updateByPrimaryKey($id, $values);

            $this->flashMessage('source_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->sourceManager->add($values);

            $this->flashMessage('source_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Source:edit', $id);
    }
}
