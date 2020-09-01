<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200416134353 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->createTable('emilys_worlds');
        $table->addColumn('id', 'guid', ['notnull' => true]);
        $table->addColumn('name', 'string', ['notnull' => true]);
        $table->addColumn('type', 'string', ['notnull' => true]);
        $table->setPrimaryKey(['id'], 'emilys_worlds_pkey');
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('emilys_worlds');

    }
}
