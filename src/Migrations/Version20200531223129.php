<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200531223129 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Creation of the app database';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tab_articles (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', category_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', author_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, summary VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, image_name VARCHAR(191) DEFAULT NULL, image_size INT NOT NULL, published_at DATETIME DEFAULT NULL, article_status VARCHAR(20) NOT NULL, comments_status VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_6FC581F7989D9B62 (slug), INDEX IDX_6FC581F712469DE2 (category_id), INDEX IDX_6FC581F7F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tab_articles_tags (articles_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', tags_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_E8699AED1EBAF6CC (articles_id), INDEX IDX_E8699AED8D7B4FB4 (tags_id), PRIMARY KEY(articles_id, tags_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tab_categories (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_74BB329989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tab_comments (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', article_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', author_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', content LONGTEXT NOT NULL, published_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_8F8626B57294869C (article_id), INDEX IDX_8F8626B5F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tab_comments_responses (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', comment_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', author_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', content LONGTEXT NOT NULL, published_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_EFD939BF8697D13 (comment_id), INDEX IDX_EFD939BF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tab_ratings (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', article_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', status VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B6106F2C7294869C (article_id), INDEX IDX_B6106F2CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tab_tags (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tab_users (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, avatar_name VARCHAR(255) DEFAULT NULL, avatar_size INT DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) NOT NULL, is_deleted TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_64579348F85E0677 (username), UNIQUE INDEX UNIQ_64579348E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tab_articles ADD CONSTRAINT FK_6FC581F712469DE2 FOREIGN KEY (category_id) REFERENCES tab_categories (id)');
        $this->addSql('ALTER TABLE tab_articles ADD CONSTRAINT FK_6FC581F7F675F31B FOREIGN KEY (author_id) REFERENCES tab_users (id)');
        $this->addSql('ALTER TABLE tab_articles_tags ADD CONSTRAINT FK_E8699AED1EBAF6CC FOREIGN KEY (articles_id) REFERENCES tab_articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tab_articles_tags ADD CONSTRAINT FK_E8699AED8D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tab_tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tab_comments ADD CONSTRAINT FK_8F8626B57294869C FOREIGN KEY (article_id) REFERENCES tab_articles (id)');
        $this->addSql('ALTER TABLE tab_comments ADD CONSTRAINT FK_8F8626B5F675F31B FOREIGN KEY (author_id) REFERENCES tab_users (id)');
        $this->addSql('ALTER TABLE tab_comments_responses ADD CONSTRAINT FK_EFD939BF8697D13 FOREIGN KEY (comment_id) REFERENCES tab_comments (id)');
        $this->addSql('ALTER TABLE tab_comments_responses ADD CONSTRAINT FK_EFD939BF675F31B FOREIGN KEY (author_id) REFERENCES tab_users (id)');
        $this->addSql('ALTER TABLE tab_ratings ADD CONSTRAINT FK_B6106F2C7294869C FOREIGN KEY (article_id) REFERENCES tab_articles (id)');
        $this->addSql('ALTER TABLE tab_ratings ADD CONSTRAINT FK_B6106F2CA76ED395 FOREIGN KEY (user_id) REFERENCES tab_users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tab_articles_tags DROP FOREIGN KEY FK_E8699AED1EBAF6CC');
        $this->addSql('ALTER TABLE tab_comments DROP FOREIGN KEY FK_8F8626B57294869C');
        $this->addSql('ALTER TABLE tab_ratings DROP FOREIGN KEY FK_B6106F2C7294869C');
        $this->addSql('ALTER TABLE tab_articles DROP FOREIGN KEY FK_6FC581F712469DE2');
        $this->addSql('ALTER TABLE tab_comments_responses DROP FOREIGN KEY FK_EFD939BF8697D13');
        $this->addSql('ALTER TABLE tab_articles_tags DROP FOREIGN KEY FK_E8699AED8D7B4FB4');
        $this->addSql('ALTER TABLE tab_articles DROP FOREIGN KEY FK_6FC581F7F675F31B');
        $this->addSql('ALTER TABLE tab_comments DROP FOREIGN KEY FK_8F8626B5F675F31B');
        $this->addSql('ALTER TABLE tab_comments_responses DROP FOREIGN KEY FK_EFD939BF675F31B');
        $this->addSql('ALTER TABLE tab_ratings DROP FOREIGN KEY FK_B6106F2CA76ED395');
        $this->addSql('DROP TABLE tab_articles');
        $this->addSql('DROP TABLE tab_articles_tags');
        $this->addSql('DROP TABLE tab_categories');
        $this->addSql('DROP TABLE tab_comments');
        $this->addSql('DROP TABLE tab_comments_responses');
        $this->addSql('DROP TABLE tab_ratings');
        $this->addSql('DROP TABLE tab_tags');
        $this->addSql('DROP TABLE tab_users');
    }
}
