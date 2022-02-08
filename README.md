# rapidapi-linkedin-to-mysql

Adapter that pulls the amount of Linkedin followers via RapidAPI and inserts it into a MySQL DB table. If you were wondering why the database has such a strange structure, that's because we blend the data with data from the Gravio IoT Database to show it all in one single Google Data Studio dashboard.

You will need a RapidAPI account and authentication token for this. Get it from https://rapidapi.com/developer/dashboard , you should get a few monthly requests for free. Then it's 0.05 per request: https://rapidapi.com/williambarberjr/api/linkedin-company-data/pricing

### Creating the database table

For this example, we created the a database using the below SQL, which is also used to store sensor data:

```sql
CREATE TABLE IF NOT EXISTS `gravio_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `AreaName` varchar(128) COLLATE latin1_general_ci NOT NULL,
  `LayerName` varchar(128) COLLATE latin1_general_ci NOT NULL,
  `DataKind` varchar(128) COLLATE latin1_general_ci NOT NULL,
  `PhysicalDeviceName` varchar(128) COLLATE latin1_general_ci NOT NULL,
  `PhysicalDeviceId` varchar(128) COLLATE latin1_general_ci NOT NULL,
  `DataId` varchar(128) COLLATE latin1_general_ci NOT NULL,
  `Timestamp` datetime NOT NULL COMMENT 'Original Sensor Timestamp',
  `Data` varchar(256) COLLATE latin1_general_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'MySQL Database Timestamp',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=8 ;
```

It's the same database as the [http post to mysql insert](https://github.com/cburgdorfer/http-post-to-mysql-insert)

Then we use a crontab to trigger the script 1x a day to pull the data and insert it into the database. This is then used to subsequently show the data in a Google Data Studio dashboard. 

For more details please check [How to push IoT sensor data from Graivo to Google Data Studio](https://www.gravio.com/en-blog/tutorial-pushing-iot-sensor-data-to-google-data-studio-to-create-a-time-series-graph)