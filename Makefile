composer ?= composer
PHPUNIT_OPTS =
symfony = symfony
php = php
docker = docker
cargo = cargo
mdbook = mdbook


help: Makefile
	@echo
	@echo " Choose a command run in Midway:"
	@echo
	@sed -n 's/^##//p' $< | column -t -s ':' |  sed -e 's/^/ /'
	@echo


## purge: Clear the cache
.PHONY: purge
purge:
	@echo ">> ============= Clear Cache ============= <<"
	-rm -rf var/cache/*
	-rm -rf var/logs/*
	-rm var/data.db


## composer: Install packages
.PHONY: composer
composer:
	@echo ">> ============= Install Packages ============= <<"
	@$(composer) install


## fix-diff: Format diff
.PHONY: fix-diff
fix-diff:
	@echo ">> ============= Fix code diff ============= <<"
	./vendor/bin/php-cs-fixer fix --diff --dry-run -v


## migrate: Migrate the database
.PHONY: migrate
migrate:
	@echo ">> ============= Run db migration ============= <<"
	@$(php) bin/console doctrine:schema:update --force


## test: Run test cases
.PHONY: test
test: purge composer migrate
	@echo ">> ============= Run test cases ============= <<"
	bin/phpunit -c . $(PHPUNIT_OPTS) --log-junit build/phpunit.xml --coverage-text


## lint: Lint all the things
.PHONY: lint
lint: purge composer migrate
	@echo ">> ============= Lint all the things ============= <<"
	@! grep -lIUr --color '^M' config/ src/ composer.json composer.lock || ( echo '[ERROR] Above files have CRLF line endings' && exit 1 )
	$(composer) validate --strict
	./bin/console lint:yaml config
	@find config -type f -name \*.yaml | while read file; do echo -n "$$file"; php bin/console --no-debug --no-interaction --env=test lint:yaml "$$file" || exit 1; done
	@find src tests -type f -name \*.php | while read file; do php -l "$$file" || exit 1; done
	./vendor/bin/phpcs
	./vendor/bin/php-cs-fixer fix --diff --dry-run -v


## coverage: Get Coverage Report
.PHONY: coverage
coverage: cc composer
	@echo ">> ============= Get Coverage Report ============= <<"
	mkdir -p build/coverage
	bin/phpunit  --log-junit build/phpunit.xml


## fix: Fix Style Issues
.PHONY: fix
fix:
	@echo ">> ============= Fix Code Format ============= <<"
	./vendor/bin/php-cs-fixer fix


## run: Run Midway
.PHONY: run
run:
	@echo ">> ============= Run App ============= <<"
	@$(symfony) serve --no-tls


## installed: Show a list of installed packages
.PHONY: installed
installed:
	@echo ">> ============= Show Installed Packages ============= <<"
	@$(composer) show -i


## outdated: Show a list of outdated packages
.PHONY: outdated
outdated:
	@echo ">> ============= Show Outdated Packages ============= <<"
	@$(composer) outdated


## db: Run a db container
.PHONY: db
db:
	@echo ">> ============= Run a docker container ============= <<"
	@$(docker) run -d --name=mysql-server \
		-p 3306:3306 \
		-v mysql-data:/var/lib/mysql \
		-e MYSQL_ROOT_PASSWORD=root mysql


## mdbook: Install mdbook rust package (Rust and Cargo needed)
.PHONY: mdbook
mdbook:
	@echo ">> ============= Install mdbook ============= <<"
	@$(cargo) install mdbook


## docs: Build docs
.PHONY: docs
docs:
	@echo ">> ============= Building docs ============= <<"
	@$(mdbook) build docs



## dasyn: Debug async messages and handlers
.PHONY: dasyn
dasyn:
	@echo ">> ============= Debug Messenger ============= <<"
	@$(php) bin/console debug:messenger


## async: Run async tasks
.PHONY: async
async:
	@echo ">> ============= Async Tasks ============= <<"
	@$(php) bin/console messenger:consume async -vv


## ci: Run CI Checks
.PHONY: ci
ci: config purge composer lint test
	@echo "All Quality Checks Passed"


.PHONY: help
