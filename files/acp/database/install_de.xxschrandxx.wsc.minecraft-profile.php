<?php

use wcf\system\database\table\column\DefaultFalseBooleanDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar191DatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\index\DatabaseTableForeignKey;

return [
    DatabaseTable::create('wcf1_minecraft_profile')
        ->columns([
            ObjectIdDatabaseTableColumn::create('profileID'),
            NotNullInt10DatabaseTableColumn::create('minecraftID'),
            NotNullVarchar191DatabaseTableColumn::create('minecraftUUID')
                ->length(36),
            NotNullVarchar191DatabaseTableColumn::create('minecraftName')
                ->length(16),
            VarcharDatabaseTableColumn::create('url')
                ->length(255),
            DefaultFalseBooleanDatabaseTableColumn::create('imageGenerated'),
            DefaultFalseBooleanDatabaseTableColumn::create('online')
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['minecraftID'])
                ->onDelete('CASCADE')
                ->referencedColumns(['minecraftID'])
                ->referencedTable('wcf1_minecraft'),
        ])
];
