<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GetIds.php
 * User: Tomáš Babický
 * Date: 10.12.2020
 * Time: 15:25
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

/**
 * Trait GetIds
 *
 * @package Rendix2\FamilyTree\App\Model\Facades
 */
trait GetIds
{

    /**
     * @param array $rows
     * @param string $column
     *
     * @return array
     */
    public function getIds(array $rows, $column)
    {
        $ids = [];

        foreach ($rows as $row) {
            $ids[] = $row->{$column};
        }

        return array_unique($ids);
    }
}