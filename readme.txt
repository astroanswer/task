﻿1. Edit client.php
Change "$url="http://127.0.0.1/task/webservice/index.php";" to the webservice URL being used

2. Edit webservice/index.php
Right at the bottom of the file, the default name list and schedule dates can be edited

"
$wheel=new SWOF(Array("Alvin","Alex","Catherine","Cecelia","Denker","Donothan","Garard","Gallifrey","Heather","Hannah"),"2018-12-17");

"


Of course, the API can be enhanced to accept name list, schedule dates and other parameters as needed.