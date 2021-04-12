<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IPersonDelete.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 1:51
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Person\Interfaces;

use Dibi\Result;
use Rendix2\FamilyTree\App\Model\Interfaces\IDeleter;

/**
 * Interface IPersonDelete
 */
interface IPersonDeleter extends IDeleter
{

    /**
     * @param int $motherId
     *
     * @return Result|int
     */
    public function deleteByMotherId($motherId);

    /**
     * @param int $fatherId
     *
     * @return Result|int
     */
    public function deleteByFatherId($fatherId);
}