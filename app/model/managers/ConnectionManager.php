<?php
/**
 *
 * Created by PhpStorm.
 * Filename: ConnectionManager.php
 * User: Tomáš Babický
 * Date: 09.02.2021
 * Time: 0:26
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Connection;

/**
 * Class ConnectionManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class ConnectionManager
{
    /**
     * @var Connection $dibi
     */
    protected $dibi;

    /**
     * ConnectionManager constructor.
     *
     * @param Connection $dibi
     */
    public function __construct(Connection $dibi)
    {
        $this->dibi = $dibi;
    }

    /**
     * @return Connection
     */
    public function getDibi()
    {
        return $this->dibi;
    }
}
