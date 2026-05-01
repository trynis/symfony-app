<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260501225200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add provider and external photo id fields to photos';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE photos ADD provider VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE photos ADD external_photo_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_photo_user_provider_external ON photos (user_id, provider, external_photo_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX uniq_photo_user_provider_external');
        $this->addSql('ALTER TABLE photos DROP provider');
        $this->addSql('ALTER TABLE photos DROP external_photo_id');
    }
}
