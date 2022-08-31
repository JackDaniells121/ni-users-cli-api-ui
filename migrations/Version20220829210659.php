<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220829210659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_skill DROP INDEX UNIQ_BCFF1F2F5A6C0D6B, ADD INDEX IDX_BCFF1F2F5A6C0D6B (skill_id_id)');
        $this->addSql('ALTER TABLE user_skill DROP skill_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_skill DROP INDEX IDX_BCFF1F2F5A6C0D6B, ADD UNIQUE INDEX UNIQ_BCFF1F2F5A6C0D6B (skill_id_id)');
        $this->addSql('ALTER TABLE user_skill ADD skill_id INT NOT NULL');
    }
}
