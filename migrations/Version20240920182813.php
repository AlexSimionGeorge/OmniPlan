<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240920182813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ADD email VARCHAR(255) NOT NULL UNIQUE');
        $this->addSql('ALTER INDEX user_username_key RENAME TO UNIQ_8D93D649F85E0677');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" DROP email');
        $this->addSql('ALTER INDEX uniq_8d93d649f85e0677 RENAME TO user_username_key');
    }
}
