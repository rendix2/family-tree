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
use Rendix2\FamilyTree\App\Filters\SourceTypeFilter;
use Rendix2\FamilyTree\App\Forms\SourceForm;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\SourceManager;
use Rendix2\FamilyTree\App\Managers\SourceTypeManager;
use Rendix2\FamilyTree\App\Model\Facades\SourceFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\Source\SourceEditDeleteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Source\SourceListDeleteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\SourceType\SourceTypeAddSourceModal;

/**
 * Class SourcePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class SourcePresenter extends BasePresenter
{
    use SourceListDeleteModal;
    use SourceEditDeleteModal;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

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
     * @param PersonManager $personManager
     * @param SourceFacade $sourceFacade
     * @param SourceManager $sourceManager
     * @param SourceTypeManager $sourceTypeManager
     */
    public function __construct(
        PersonManager $personManager,
        SourceFacade $sourceFacade,
        SourceManager $sourceManager,
        SourceTypeManager $sourceTypeManager
    ) {
        parent::__construct();

        $this->personManager = $personManager;
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

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        $this->template->addFilter('sourceType', new SourceTypeFilter());
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());
        $sourceTypes = $this->sourceTypeManager->getPairsCached('name');

        $this['form-personId']->setItems($persons);
        $this['form-sourceTypeId']->setItems($sourceTypes);

        if ($id !== null) {
            $source = $this->sourceFacade->getByPrimaryKeyCached($id);

            if (!$source) {
                $this->error('Item not found.');
            }

            $this['form-personId']->setDefaultValue($source->person->id);
            $this['form-sourceTypeId']->setDefaultValue($source->sourceType->id);
            $this['form']->setDefaults((array)$source);
        }
    }

    /**
     * @return Form
     */
    protected function createComponentForm()
    {
        $formFactory = new SourceForm($this->getTranslator());

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
            $this->sourceManager->updateByPrimaryKey($id, $values);

            $this->flashMessage('source_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->sourceManager->add($values);

            $this->flashMessage('source_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Source:edit', $id);
    }
}
