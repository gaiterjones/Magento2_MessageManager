
# GAITERJONES MESSAGE MANAGER
Magento 2 module to manage, configure and test RabbitMQ AMQP message queues.

https://blog.gaiterjones.com/magento-2-asynchronous-message-queue-management/

Can also be used with my Docker consumer containers.

https://github.com/gaiterjones/docker-magento2


## Commands

 - bin/magento messagemanager:getconsumers
	 - get all consumers
 - switches
	 -  --json
		 - get all consumers in json format `bin/magento messagemanager:getconsumers --json`
	  - --whitelist
		 - get all whitelisted consumers `bin/magento messagemanager:getconsumers --json --whitelist`

 - bin/magento messagemanager:getconfig
	 - display current queue config
 - switches
	 -  --buildconfig
		 - build and display new AMQP queue config to convert MySQL queues to AMQP `bin/magento messagemanager:getconfig --buildconfig`
	  - --whitelist
		 - build display new AMQP queue config to convert whitelisted queues to AMQP `bin/magento messagemanager:getconfig --buildconfig --whitelist`
	  - --saveconfig
		 - build and save new AMQP queue config to convert queues to AMQP `bin/magento messagemanager:getconfig --buildconfig --whitelist --saveconfig`

 - bin/magento messagemanager:testqueue
	 - send a test message to `gaiterjones.magento.message.manager`

Whitelist and Blacklists are defined in `Gaiterjones\MessageManager\Helper\Data`

Backup your env.php before making any changes.

## Docker consumer container
**docker-compose.yml**

        ...
        restart: always
        entrypoint: ["/usr/local/start_consumer.sh"]
        environment:
          - CONSUMER_WHITELIST=true
        ...

**start_consumer.sh**

https://github.com/gaiterjones/docker-magento2/blob/master/magento2/consumer/start_consumer.sh
