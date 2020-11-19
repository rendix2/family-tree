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
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\SourceManager;
use Rendix2\FamilyTree\App\Managers\SourceTypeManager;
use Rendix2\FamilyTree\App\Model\Facades\SourceFacade;

/**
 * Class SourcePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class SourcePresenter extends BasePresenter
{
    use CrudPresenter {
        actionEdit as traitActionEdit;
    }

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var SourceFacade $sourceFacade
     */
    private $sourceFacade;

    /**
     * @var SourceManager $manager
     */
    private $manager;

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
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addText('link', 'source_link')
            ->setRequired('source_link_required');

        $form->addSelect('personId', $this->getTranslator()->translate('source_person'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('source_select_person'))
            ->setRequired('source_person_required');

        $form->addSelect('sourceTypeId', $this->getTranslator()->translate('source_type'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('source_select_type'))
            ->setRequired('source_source_type_required');

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
