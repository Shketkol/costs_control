<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180315182254 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE account_type ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE account_type ADD CONSTRAINT FK_4DD083A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_4DD083A76ED395 ON account_type (user_id)');

        // Add common types of accounts
        $this->addSql('INSERT INTO account_type VALUES (NULL, "cash", NULL)');
        $this->addSql('INSERT INTO account_type VALUES (NULL, "card", NULL)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Delete common types of accounts
        $this->addSql('DELETE FROM account_type WHERE name IN("cash", "card")');

        $this->addSql('ALTER TABLE account_type DROP FOREIGN KEY FK_4DD083A76ED395');
        $this->addSql('DROP INDEX IDX_4DD083A76ED395 ON account_type');
        $this->addSql('ALTER TABLE account_type DROP user_id');
    }
}
