<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonSelectForm.php
 * User: Tomáš Babický
 * Date: 03.11.2020
 * Time: 17:11
 */

namespace Rendix2\FamilyTree\App\Controls\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\BootstrapRenderer;

/**
 * Class PersonSelectForm
 *
 * @package Rendix2\FamilyTree\App\Controls\Forms
 */
class PersonSelectForm
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * PersonSelectForm constructor.
     *
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return Form
     */
    public function create()
    {
        $form = new Form();

        $form->setTranslator($this->translator);

        $form->addProtection();

        $form->addHidden('personId');

        $form->addSelect('selectedPersonId', $this->translator->translate('person_person'))
            ->setTranslator()
            ->setPrompt($this->translator->translate('person_select_person'))
            ->setRequired('person_person_required');

        $form->addSubmit('yes', 'person_select')
            ->setAttribute('class', 'ajax');

        $form->elementPrototype->setAttribute('class', 'ajax');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
