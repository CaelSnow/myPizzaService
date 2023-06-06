start:
	docker-compose up -d

console:
	docker exec -it ewa_${MODE}_php_apache bash

stop:
	docker-compose down

build:
	docker-compose down -v
	docker-compose build
	docker-compose up -d --force-recreate mariadb
	docker-compose up -d

clean:
	docker rm -v --force ewa_${MODE}_php_apache
	docker rm -v --force ewa_${MODE}_mariadb
	docker rm -v --force ewa_${MODE}_phpmyadmin
	docker network rm ewa_${MODE}_net
	
cleanall:
	docker system prune -a