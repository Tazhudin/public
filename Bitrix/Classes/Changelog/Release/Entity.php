<?php
namespace Dev05\Classes\Changelog\Release;

use Dev05\Classes\HighloadBlock;

trait Entity
{
    use HighloadBlock\Entity;

    /**
     * @var string
     */
    public $hlTableName = 'hl_changelog_releases_table';
}