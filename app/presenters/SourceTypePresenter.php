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
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Managers\SourceTypeManager;

/**
 * Class SourceTypePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class SourceTypePresenter extends BasePresenter
{
    use CrudPresenter;

    /**
     * @var SourceTypeManager $manager
     */
    private $manager;

    /**
     * SourceTypePresenter constructor.
     *
     * @param SourceTypeManager $sourceTypeManager
     */
    public function __construct(SourceTypeManager $sourceTypeManager)
    {
        parent::__construct();

        $this->manager = $sourceTypeManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $sourceTypes = $this->manager->getAll();

        $this->template->sourceTypes = $sourceTypes;
    }

    protected function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addText('name', 'source_type_name');

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
