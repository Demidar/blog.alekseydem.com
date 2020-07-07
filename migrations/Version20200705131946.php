<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200705131946 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE section_closure (id INT AUTO_INCREMENT NOT NULL, ancestor INT NOT NULL, descendant INT NOT NULL, depth INT NOT NULL, INDEX IDX_9A4040DEB4465BB (ancestor), INDEX IDX_9A4040DE9A8FAD16 (descendant), INDEX IDX_436FB069ECCE66B8 (depth), UNIQUE INDEX IDX_5F71F6FC06B6F9C1 (ancestor, descendant), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE section_closure ADD CONSTRAINT FK_9A4040DEB4465BB FOREIGN KEY (ancestor) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE section_closure ADD CONSTRAINT FK_9A4040DE9A8FAD16 FOREIGN KEY (descendant) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE section ADD language VARCHAR(255) NOT NULL, ADD level INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE section_closure');
        $this->addSql('ALTER TABLE section DROP language, DROP level');
    }
}
