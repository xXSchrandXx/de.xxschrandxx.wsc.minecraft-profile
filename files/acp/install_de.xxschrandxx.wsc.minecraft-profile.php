<?php

use wcf\system\database\table\DatabaseTableChangeProcessor;
use wcf\system\database\table\PartialDatabaseTable;
use wcf\system\WCF;

$tables = [
    PartialDatabaseTable::create('wcf1_user_minecraft')
        ->columns([
            // TODO Fetch images?
        ])
];

(new DatabaseTableChangeProcessor(
    $this->installation->getPackage(),
    $tables,
    WCF::getDB()->getEditor()
))->process();
