<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260501223800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create user photo provider credentials table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_photo_provider_credentials (
            id SERIAL PRIMARY KEY,
            user_id INTEGER NOT NULL,
            provider VARCHAR(50) NOT NULL,
            token VARCHAR(255) NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            CONSTRAINT fk_user_photo_provider_credentials_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )');

        $this->addSql('CREATE UNIQUE INDEX uniq_user_provider ON user_photo_provider_credentials (user_id, provider)');
        $this->addSql('CREATE INDEX idx_user_photo_provider_credentials_user_id ON user_photo_provider_credentials (user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user_photo_provider_credentials');
    }
}
