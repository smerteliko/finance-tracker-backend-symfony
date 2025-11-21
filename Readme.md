# Finance Tracker Backend (Symfony Edition)

## 🌟 Project Overview

**Finance Tracker** is a modern personal finance management system designed as a full-stack application. The backend, built with **Symfony 7.3**, provides a robust, secure, and scalable REST API for managing financial transactions, analyzing income and expenses, and generating reports.

This project showcases best practices in the PHP ecosystem, including layered architecture, security with JWT, robust Doctrine ORM mapping (using UUIDs), and a highly automated development setup using Docker.

## 🔗 Project Ecosystem Links

This repository is part of a larger full-stack project, designed for technology comparison and demonstration:

| Project | Role | Repository Link |
| :--- | :--- | :--- |
| **Frontend** (Vue.js) | Client Application (UI) | `https://github.com/smerteliko/finance-tracker-frontend-vue` |
| **Sibling Backend** (Spring) | Alternative Backend Implementation | `https://github.com/smerteliko/finance-tracker-backend-spring` |

## 🚀 Key API Features (Extended)

* **Authentication & Security:** A reliable user registration and login system secured with **JWT** (JSON Web Tokens). All protected routes rely on the Bearer token standard.
* **Transaction Management (CRUD):** Full Create, Read, Update, and Delete operations for transactions.
* **Data Integrity & Consistency:** Critical transaction operations (Create, Update, Delete) are handled within **atomic transactions** (Unit of Work) to ensure the account **balance is always updated** immediately and correctly.
* **Account Management:** Full CRUD support for user-specific financial accounts (Checking, Savings, Cash).
* **Real-time Analytics:** Optimized endpoints to retrieve comprehensive financial analytics, including total balance, total income and expenses, and a breakdown by category for any given period.
* **Report Generation:** The ability to export filtered financial data into **CSV** format (readiness for further PDF processing is implied).
* **Centralized Error Handling:** Standardized global JSON error response for all API failures (400, 401, 404, 409).

## 🛠️ Technology Stack

| Component | Technology / Bundle | Purpose |
| :--- | :--- | :--- |
| **Framework** | Symfony 7.3 | PHP Application Framework |
| **Database** | **PostgreSQL 15** | Primary data persistence |
| **ORM & Persistence** | **Doctrine ORM**, **Ramsey UUID Doctrine** | Mapping, Querying, and using UUIDs as primary keys |
| **Security** | **LexikJWTAuthenticationBundle** | JWT token handling and firewall setup |
| **Schema Management** | **Doctrine Migrations** | Database schema versioning |
| **API Documentation** | **NelmioApiDocBundle** | Interactive API documentation (Swagger UI) |
| **Containerization** | Docker and Docker Compose | Isolated development environment |
| **Testing** | **PHPUnit** | Unit and functional testing |

## 📐 Expanded Project Structure

The codebase is strictly structured based on Symfony and DDD principles to ensure maintainability and testability:

| Directory | Content & Responsibility |
| :--- | :--- |
| `src/Entity` | **Data Models:** Doctrine Entities (`User`, `Account`, `Transaction`, `Category`), using UUIDs as PKs and serialization groups (`#[Groups]`). |
| `src/Repository` | **Data Access Layer:** Contains complex DQL queries for analytics, filtering, and pagination. |
| `src/DTO` | **Data Contracts:** All request and response schemas (`*Request`, `*Response`, `PaginatedResponse`, `AnalyticsRequest`). |
| `src/Service` | **Business Logic Layer:** Contains all core domain logic (`AuthService`, `TransactionService`, `AccountService`). Responsible for data integrity (e.g., updating account balances). |
| `src/Controller` | **Routing/API Layer:** Handles HTTP requests, validation (`MapRequestPayload`), authorization (`Voters`), and returns serialized Entity/DTO responses. |
| `src/Security` | **Authorization:** Contains `*Voter` classes (`AccountVoter`, `TransactionVoter`, etc.) enforcing resource ownership rules. |
| `src/DataFixtures` | **Test Data:** Provides a large, synchronized dataset for realistic testing. |
| `docker` | **Infrastructure:** Nginx and PHP-FPM configuration and setup scripts. |

## ⚙️ Getting Started

### 1\. Prerequisites

You must have **Docker** and **Docker Compose** installed.

### 2\. Running the Application Locally

The development setup is automated to handle migrations and fixtures upon startup.

1.  **Clone the repository:**

    ```bash
    git clone https://github.com/smerteliko/finance-tracker-backend-symfony.git
    cd finance-tracker-backend-symfony
    ```

2.  **Start the Docker containers:**

    ```bash
    docker compose up --build
    ```

    *(This command automatically builds the PHP image, starts PostgreSQL, runs migrations, and executes fixtures.)*

3.  **Access the API and Documentation:**

    * **Application Root:** `http://localhost:8080/`
    * **Documentation (Swagger UI):** `http://localhost:8080/api/doc`

### 3\. Running Tests

Tests must be executed inside the PHP container against the test database environment.

1.  **Enter the PHP container shell:**
    ```bash
    docker exec -it finance-tracker-symfony-php /bin/sh
    ```
2.  **Run PHPUnit:**
    ```bash
    php bin/phpunit
    ```

-----

## 👨‍💻 Contact

* **Name:** Nikolay Makarov
* **GitHub:** `https://github.com/smerteliko`
* **LinkedIn:** `https://www.linkedin.com/in/nikolay-makarov/`
