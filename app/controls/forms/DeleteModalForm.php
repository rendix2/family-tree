<?php
/**
 *
 * Created by PhpStorm.
 * Filename: DeleteModalForm.php
 * User: Tomáš Babický
 * Date: 25.10.2020
 * Time: 22:52
 */

namespace Rendix2\FamilyTree\App\Controls\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalSettings;

/**
 * Class DeleteModalForm
 *
 * @package Rendix2\FamilyTree\App\Controls\Forms
 */
class DeleteModalForm
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * DeleteModalForm constructor.
     *
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param DeleteModalSettings $deleteModalSettings
     *
     * @return Form
     */
    public function create(DeleteModalSettings $deleteModalSettings)
    {
        $form = new Form();

        $form->setTranslator($this->translator);

        $form->addProtection();

        if ($deleteModalSettings->httpRedirect) {
            $form->addSubmit('yes', 'modal_delete')
                ->setAttribute('class', 'ajax btn btn-danger modal-ok')
                ->setAttribute('data-dismiss', 'modal')
                ->setAttribute('data-naja-force-redirect', '')
                ->onClick[] = $deleteModalSettings->callBack;
        } else {
            $form->addSubmit('yes', 'modal_delete')
                ->setAttribute('class', 'ajax btn btn-danger modal-ok')
                ->setAttribute('data-dismiss', 'modal')
                ->onClick[] = $deleteModalSettings->callBack;
        }

        $form->addSubmit('no','modal_storno')
            ->setAttribute('class', 'btn btn-primary close-modal')
            ->setAttribute('data-dismiss', 'modal')
            ->setAttribute('aria-label', 'Close');

        return $form;
    }
}
