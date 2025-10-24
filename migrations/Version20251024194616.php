<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251024194616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create initial database schema for Finance Tracker';
    }

    public function up(Schema $schema): void
    {
        // Create users table
        $this->addSql('CREATE TABLE users (
            id SERIAL PRIMARY KEY,
            first_name VARCHAR(255) NOT NULL,
            last_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            currency VARCHAR(20) DEFAULT \'USD\',
            created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL
        )');

        // Create categories table
        $this->addSql('CREATE TABLE categories (
            id SERIAL PRIMARY KEY,
            user_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            color VARCHAR(7) NOT NULL,
            type VARCHAR(20) NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
            CONSTRAINT fk_category_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
        )');

        // Create transactions table
        $this->addSql('CREATE TABLE transactions (
            id SERIAL PRIMARY KEY,
            user_id INT NOT NULL,
            category_id INT NOT NULL,
            amount NUMERIC(15, 2) NOT NULL,
            type VARCHAR(20) NOT NULL,
            description VARCHAR(500),
            date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
            CONSTRAINT fk_transaction_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
            CONSTRAINT fk_transaction_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE
        )');

        // Create indexes for better performance
        $this->addSql('CREATE INDEX idx_users_email ON users (email)');
        $this->addSql('CREATE INDEX idx_categories_user_id ON categories (user_id)');
        $this->addSql('CREATE INDEX idx_categories_type ON categories (type)');
        $this->addSql('CREATE INDEX idx_transactions_user_id ON transactions (user_id)');
        $this->addSql('CREATE INDEX idx_transactions_category_id ON transactions (category_id)');
        $this->addSql('CREATE INDEX idx_transactions_date ON transactions (date)');
        $this->addSql('CREATE INDEX idx_transactions_type ON transactions (type)');
        $this->addSql('CREATE INDEX idx_transactions_user_date ON transactions (user_id, date)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE transactions');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE users');
    }
}
