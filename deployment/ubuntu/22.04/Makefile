PYTHON ?= python
GALAXY ?= ansible-galaxy


help: Makefile
	@echo
	@echo " Choose a command run in Oxygen:"
	@echo
	@sed -n 's/^##//p' $< | column -t -s ':' |  sed -e 's/^/ /'
	@echo


## config: Install dependencies.
config:
	$(PYTHON) -m pip install ansible
	$(PYTHON) -m pip install ansible-lint
	$(GALAXY) install -r requirements.yml


## prod: Configure the server.
prod:
	@echo "\n>> ============= Run ============= <<"
	ansible-playbook prod-playbook.yml --ask-vault-pass


## helium: Deploy Helium application.
helium:
	@echo "\n>> ============= Run ============= <<"
	ansible-playbook helium-playbook.yml --ask-vault-pass


.PHONY: help prod config helium
