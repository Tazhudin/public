<?php
namespace Dev05\Classes\Changelog\Feature;

use Dev05\Classes\HighloadBlock;

trait Entity
{
    use HighloadBlock\Entity;

    /**
     * @var string
     */
    public $hlTableName = 'hl_changelog_changes_table';
}