<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200710081555 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment_closure (id INT AUTO_INCREMENT NOT NULL, ancestor INT NOT NULL, descendant INT NOT NULL, depth INT NOT NULL, INDEX IDX_BB74EFC1B4465BB (ancestor), INDEX IDX_BB74EFC19A8FAD16 (descendant), INDEX IDX_C9641573A128105C (depth), UNIQUE INDEX IDX_24C6891067AA1177 (ancestor, descendant), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment_closure ADD CONSTRAINT FK_BB74EFC1B4465BB FOREIGN KEY (ancestor) REFERENCES comment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment_closure ADD CONSTRAINT FK_BB74EFC19A8FAD16 FOREIGN KEY (descendant) REFERENCES comment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD level INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE comment_closure');
        $this->addSql('ALTER TABLE comment DROP level');
    }
}
