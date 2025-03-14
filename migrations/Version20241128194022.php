<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241128194022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_vtuber (product_id INT NOT NULL, vtuber_id INT NOT NULL, INDEX IDX_B3A488C64584665A (product_id), INDEX IDX_B3A488C6BF22EC4E (vtuber_id), PRIMARY KEY(product_id, vtuber_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vtuber (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_vtuber ADD CONSTRAINT FK_B3A488C64584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_vtuber ADD CONSTRAINT FK_B3A488C6BF22EC4E FOREIGN KEY (vtuber_id) REFERENCES vtuber (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_vtuber DROP FOREIGN KEY FK_B3A488C64584665A');
        $this->addSql('ALTER TABLE product_vtuber DROP FOREIGN KEY FK_B3A488C6BF22EC4E');
        $this->addSql('DROP TABLE product_vtuber');
        $this->addSql('DROP TABLE vtuber');
    }
}
