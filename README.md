# Postgre SQL DB Writer

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/keboola/db-writer-pgsql/blob/master/LICENSE.md)

This component exports data from Keboola to a PostgreSQL database.

## Example Configuration

```json
    {
      "db": {        
        "host": "HOST",
        "port": "PORT",
        "database": "DATABASE",
        "user": "USERNAME",
        "password": "PASSWORD",
        "ssh": {
          "enabled": true,
          "keys": {
            "private": "ENCRYPTED_PRIVATE_SSH_KEY",
            "public": "PUBLIC_SSH_KEY"
          },
          "sshHost": "PROXY_HOSTNAME"
        }
      },
      "tables": [
        {
          "tableId": "simple",
          "dbName": "simple",
          "export": true, 
          "incremental": true,
          "primaryKey": ["id"],
          "items": [
            {
              "name": "id",
              "dbName": "id",
              "type": "int",
              "size": null,
              "nullable": null,
              "default": null
            },
            {
              "name": "name",
              "dbName": "name",
              "type": "nvarchar",
              "size": 255,
              "nullable": null,
              "default": null
            },
            {
              "name": "glasses",
              "dbName": "glasses",
              "type": "nvarchar",
              "size": 255,
              "nullable": null,
              "default": null
            }
          ]                                
        }
      ]
    }
```

## Development

This application is developed locally using Test-Driven Development (TDD).

**Steps to set up:**

1. Clone the repository: `git clone git@github.com:keboola/db-writer-pgsql.git`
2. Navigate to the project directory: `cd db-writer-pgsql`
3. Install dependencies: `docker-compose run --rm dev composer install`
4. Develop
5. Run tests: `docker-compose run --rm dev composer tests` or all checks: `docker-compose run --rm dev composer ci`
