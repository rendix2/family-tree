<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonShowImageModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:18
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;


use Nette\Application\UI\Control;
use Rendix2\FamilyTree\App\Managers\FileManager;

/**
 * Class PersonShowImageModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonShowImageModal extends Control
{
    /**
     * @var FileManager $fileManager
     */
    private $fileManager;

    /**
     * PersonShowImageModal constructor.
     *
     * @param FileManager $fileManager
     */
    public function __construct(FileManager $fileManager)
    {
        parent::__construct();

        $this->fileManager = $fileManager;
    }

    /**
     * @param int $fileId
     */
    public function handlePersonShowImage($fileId)
    {
        $presenter = $this->presenter;

        $fileModalItem = $this->fileManager->getByPrimaryKeyCached($fileId);

        $this->presenter->template->modalName = 'personShowImage';
        $this->presenter->template->fileModalItem = $fileModalItem;

        $this->presenter->payload->showModal = true;

        $this->presenter->redrawControl('modal');
    }
}
