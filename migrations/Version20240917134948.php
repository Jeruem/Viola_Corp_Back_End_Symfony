<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240917134948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture ADD viola_id INT NOT NULL');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89AEC9E5CF FOREIGN KEY (viola_id) REFERENCES viola (id)');
        $this->addSql('CREATE INDEX IDX_16DB4F89AEC9E5CF ON picture (viola_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89AEC9E5CF');
        $this->addSql('DROP INDEX IDX_16DB4F89AEC9E5CF ON picture');
        $this->addSql('ALTER TABLE picture DROP viola_id');
    }
}
