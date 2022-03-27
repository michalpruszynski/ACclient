down: 
	docker-compose down --remove-orphans

up:
	docker-compose up -d
	
build:
	docker-compose build --pull --no-cache

importdb:
	bin/console doctrine:migrations:migrate