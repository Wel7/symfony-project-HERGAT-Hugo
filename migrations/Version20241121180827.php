<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241121180827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_item ADD order_i_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09B39B41A0 FOREIGN KEY (order_i_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_52EA1F09B39B41A0 ON order_item (order_i_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F09B39B41A0');
        $this->addSql('DROP INDEX IDX_52EA1F09B39B41A0 ON order_item');
        $this->addSql('ALTER TABLE order_item DROP order_i_id');
    }
}
