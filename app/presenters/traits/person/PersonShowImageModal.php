<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonShowImageModal.php
 * User: Tomáš Babický
 * Date: 30.01.2021
 * Time: 17:57
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

/**
 * Trait PersonShowImageModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonShowImageModal
{
    /**
     * @param int $fileId
     */
    public function handlePersonShowImage($fileId)
    {
        $fileModalItem = $this->fileManager->getByPrimaryKeyCached($fileId);

        $this->template->modalName = 'personShowImage';
        $this->template->fileModalItem = $fileModalItem;

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }
}
