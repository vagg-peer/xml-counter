# xml-counter
Simple xml product counter

This plugin parse the skroutz.xml file from /wp-content/uploads/feed/ and counts the number of the products. 


If the difference between the products is above 30%, an email notification is send to the adminstrator for further investigation.


This plugin automatically counts the products once a day implemented, with wp-cron so the website needs to be loaded at least once a day.


Also the user can force plugin to count again from XMLCounter admin panel

Instalation:

1. Download the project .zip from this repository, and install it throu wordpress plugin installation tool.

OR

2. Upload and extract the .zip from this repository via FTP in folder "wp-content/plugins", and activated in "Plugins > Installed Plugins" in admin dashboard

