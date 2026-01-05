<?php

use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\index\DatabaseTableForeignKey;
use wcf\system\database\table\PartialDatabaseTable;

return [
    PartialDatabaseTable::create('wcf1_user')
        ->columns([
            IntDatabaseTableColumn::create('minecraftProfileAvatarID')
                ->length(10)
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['minecraftProfileAvatarID'])
                ->onDelete('SET NULL')
                ->referencedColumns(['profileID'])
                ->referencedTable('wcf1_minecraft_profile')
        ])
];
