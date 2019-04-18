Simple Growatt proxy with extraction of data and uploading that data to Domoticz, InfluxDB and/or PVOutput.

Nothing fancy, just quick and dirty.

run.sh is ran in a screen session. Will auto restart so the proxy will stay alive when errored.

run_upload.sh is ran using a cronjob every minute and will only upload to pvoutput if more than 6 records are available (to prevent api overuse)
