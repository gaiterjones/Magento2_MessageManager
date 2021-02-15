
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

    #!/bin/bash
    module='/var/www/dev/magento2/app/code/Gaiterjones/MessageManager'
    if [[ ! -d $module ]]; then
        echo "MessageManager module not installed!"
        exit 1
    fi

    if [ "$CONSUMER_WHITELIST" = true ]; then
        echo "Loading consumers with whitelist..."
        consumersjson=$(php /var/www/dev/magento2/bin/magento messagemanager:getconsumers --whitelist --json)
    else
        echo "Loading consumers..."
        consumersjson=$(php /var/www/dev/magento2/bin/magento messagemanager:getconsumers --json)
    fi

    consumers=$(jq -r .[] <<< "$consumersjson")
    echo $consumers

    if [ ${#consumers[@]} -eq 0 ]; then
        echo "No consumers found."
        exit 1
    fi

    for consumer in $consumers;
    do
        php /var/www/dev/magento2/bin/magento queue:consumers:start $consumer &
        status=$?
        if [ $status -ne 0 ]; then
          echo "Failed to start $consumer: $status"
          exit $status
        else
          echo "Loading $consumer ..."
        fi
    done

    echo "All consumers loaded, monitoring consumer processes..."

    while sleep 60; do

    	for consumer in $consumers;
    	do
    	  ps aux |grep $consumer |grep -q -v grep
    	  PROCESS_STATUS=$?
    	  if [ $PROCESS_STATUS -ne 0 ]; then
    		echo "Consumer $consumer has stopped, restarting..."
    		exit 1
    	  fi
    	done

    done
