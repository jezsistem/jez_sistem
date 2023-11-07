exportsql:
	docker exec mysql sh -c 'exec mysqldump jez_erp_1 -uroot -p"99Xpro88"' > /home/ghalyfadhillah/Documents/jez_erp_1.sql
importsql: 
	docker exec -i mysql sh -c 'exec mysql -uroot -p"99Xpro88" jez_erp_1' < /home/ghalyfadhillah/Documents/jez_db_new.sql
query: 
	docker exec -it mysql bash
.PHONY: exportsql importsql query