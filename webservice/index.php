<?
header('Content-type: application/json');
date_default_timezone_set("Asia/Kuala_Lumpur");
define("SWOF_DEBUG",0);



class SWOF{

private $engineers = Array();
private $work_dates = Array();
private $schedule_last_date="";
	
function 	__construct($engineers,$schedule_last_date){
	              $this->engineers=$engineers;  //fill the engineers array
	              shuffle($this->engineers);
	              $this->schedule_last_date=$schedule_last_date;
	              $this->generate_work_dates(); //fill the work dates array
	              $this->generate_schedule();
                                 }

/*
generate work dates into $this->work_dates array
will base first working day on the approaching Monday
will not consider today as the start of the schedule if today is a Monday as the day has started
assume there are no public holidays during the work schedule dates
*/	
private function generate_work_dates(){
	
$stamp=Time();
$dt=date("Y-m-d",$stamp) . ' 00:00:00';

$dt_obj=new DateTime($dt);
$start_of_today_stamp=$dt_obj->getTimestamp();
$start_of_tomorrow_stamp=$start_of_today_stamp+(60*60*24);
$start_of_next_day_stamp=$start_of_tomorrow_stamp;
while (true){
$day=date("D",$start_of_next_day_stamp);	
if ($day=='Mon'){break;}
$start_of_next_day_stamp+=(60*60*24);
}

//At this point, $start_of_next_day_stamp contains the timestamp of the next Monday
//We will assume week work schedule = Mon-Friday
$schedule_last_date_stamp=new Datetime($this->schedule_last_date . ' 00:00:00');
$schedule_last_date_stamp=$schedule_last_date_stamp->getTimestamp();


$running_workdate_index=0;
while (true){


if ( $start_of_next_day_stamp > $schedule_last_date_stamp)   {
	break;
}


$dayofweek=date("D",$start_of_next_day_stamp);
if (($dayofweek!='Sat') AND ($dayofweek!='Sun')){
$work_dates[$running_workdate_index][0]=date("Y-m-d",$start_of_next_day_stamp);
$work_dates[$running_workdate_index][1]='';
$work_dates[$running_workdate_index][2]='';
$running_workdate_index++;
                                    }


$start_of_next_day_stamp+=(60*60*24);



                            }
                            
                            
$this->work_dates=$work_dates;

/*
var_dump($work_dates);
print "<br><br>";
var_dump($this->work_dates);
exit;
*/
                                     }
	
private function generate_schedule(){

$index_engineers=sizeof($this->engineers)-1;
$sz_workdates=sizeof($this->work_dates);




$return_arr=Array();
for ($workdate_index=0;$workdate_index<$sz_workdates;$workdate_index++){

$r=rand(1,2);
if ($r==1){$a=1;$b=2;} else {$a=2;$b=1;}
	
$two_weeks_qualified_list=$this->get_engineers_who_has_not_completed_two_shifts_in_two_weeks($workdate_index);
$not_worked_today=$this->get_engineers_who_has_not_worked_today($workdate_index,$two_weeks_qualified_list);
$not_worked_yesterday=$this->get_engineers_who_has_not_worked_yesterday($workdate_index,$not_worked_today);
$selected=$this->get_random_engineer($workdate_index,$not_worked_yesterday);
$this->work_dates[$workdate_index][$a]=$selected;

$two_weeks_qualified_list=$this->get_engineers_who_has_not_completed_two_shifts_in_two_weeks($workdate_index);
$not_worked_today=$this->get_engineers_who_has_not_worked_today($workdate_index,$two_weeks_qualified_list);
$not_worked_yesterday=$this->get_engineers_who_has_not_worked_yesterday($workdate_index,$not_worked_today);
$selected=$this->get_random_engineer($workdate_index,$not_worked_yesterday);
$this->work_dates[$workdate_index][$b]=$selected;



/*
print $this->work_dates[$workdate_index][0] . ": AM=" .  $this->work_dates[$workdate_index][1] . ', PM=' .  $this->work_dates[$workdate_index][2];
print '<br><br>';
*/

$return_arr[$workdate_index]["schedule"]=$this->work_dates[$workdate_index][0];
$return_arr[$workdate_index]["AM"]=$this->work_dates[$workdate_index][1];
$return_arr[$workdate_index]["PM"]=$this->work_dates[$workdate_index][2];

	

                                                     }

$json=json_encode($return_arr);	
print $json;



                                    }
                                    
                                    

                                                                       
private function get_engineers_who_has_not_worked_yesterday($workdate_index,$engineers_arr){
	
	
if ($workdate_index==0) {

if (SWOF_DEBUG==1) {
print '<br>Not Worked Yesterday Filtered List' . "<br>";

$s=sizeof($engineers_arr);
for ($eng=0;$eng<$s;$eng++){
	
print $engineers_arr[$eng] . "<br>";	
}	

             }

	return $engineers_arr;
	
	}

$workdate_index--;


$s=sizeof($engineers_arr);
for ($eng=0;$eng<$s;$eng++){
	
$next_engineer=$engineers_arr[$eng];
if (($this->work_dates[$workdate_index][1]!=$next_engineer) AND  ($this->work_dates[$workdate_index][2]!=$next_engineer)){
	
$filtered_list[]=$next_engineer;	
                                                                                                                         }
	
                           }
                           
                           
if (SWOF_DEBUG==1) {
print "<br>";
print 'Not Worked Yesterday Filtered List' . "<br>";
$s=sizeof($filtered_list);
for ($eng=0;$eng<$s;$eng++){
	
print $filtered_list[$eng] . "<br>";	
}
                  }


return $filtered_list;	
	
                                                                     }
                                                                     
                                                                     
                                                                     
                                                                     
private function get_engineers_who_has_not_worked_today($workdate_index,$engineers_arr){
$s=sizeof($engineers_arr);
for ($eng=0;$eng<$s;$eng++){
	
$next_engineer=$engineers_arr[$eng];
if (($this->work_dates[$workdate_index][1]!=$next_engineer) AND  ($this->work_dates[$workdate_index][2]!=$next_engineer)){
	
$filtered_list[]=$next_engineer;	
                                                                                                                         }
	
                           }

if (SWOF_DEBUG==1) {
print "<br>";
print 'Not Worked Today Filtered List' . "<br>";
$s=sizeof($filtered_list);
for ($eng=0;$eng<$s;$eng++){
	
print $filtered_list[$eng] . "<br>";	
}

                   }

return $filtered_list;	
                                                                                       }
                                                                                      
                                                           
//returns array list of engineers who has not completed two shifts in last 2 weeks
private function get_engineers_who_has_not_completed_two_shifts_in_two_weeks($workdate_index){
$s=sizeof($this->engineers);

if ($workdate_index>9){
$two_weeks_ago_index=$workdate_index-9;
                        } else {
$two_weeks_ago_index=0;
                               }


for ($eng=0;$eng<$s;$eng++){
$next_engineer=$this->engineers[$eng];	
$hash["$next_engineer"]=0;

for ($checkindex=$two_weeks_ago_index;$checkindex<=$workdate_index;$checkindex++){

//print $checkindex . "<br>";	

if ($this->work_dates[$checkindex][1]==$next_engineer){
                         $hash["$next_engineer"]++;
                                                              }
if ($this->work_dates[$checkindex][2]==$next_engineer){
                         $hash["$next_engineer"]++;
                                                              }                                                              
	
                                                                                 }
	
                           }
//second pass

if (SWOF_DEBUG==1) {
print '<br>Not completed 2 shifts in 2 weeks list' . "<br>";
                    }
for ($eng=0;$eng<$s;$eng++){
$next_engineer=$this->engineers[$eng];		

if ($hash["$next_engineer"]<2)   {

$shift_qualified_engineers[]=$next_engineer;
if (SWOF_DEBUG==1) {
print $next_engineer . ' - ' . $hash["$next_engineer"] . "<br>";
                   }

	
                                 }
                           }

if (!isset ($shift_qualified_engineers)) {
	
$shift_qualified_engineers=$this->engineers;
//shuffle($shift_qualified_engineers);
}
                           
return 	$shift_qualified_engineers;
                                                                                            }

                                                           
//just get a random engineer without any conditional checks
private function get_random_engineer($workdate_index,$engineers_list){



$longest_days_service=0;
$longest_days_service_engineer="";

if ($workdate_index>=9){
$two_weeks_ago_index=$workdate_index-9;
                        } else {
$two_weeks_ago_index=0;
                               }
                               

$s=sizeof($engineers_list);

for ($eng=0;$eng<$s;$eng++){
$longest_day_for_this_engineer=0;
$next_eng=$engineers_list[$eng];


for ($running_check=$workdate_index;$running_check>=$two_weeks_ago_index;$running_check--)	{
	

if (($this->work_dates[$running_check][1]==$next_eng) OR ($this->work_dates[$running_check][2]==$next_eng)) {

$longest_day_for_this_engineer=$workdate_index-$running_check;


                                                                                                            }

                                                                                           }
$engineers_array[]=$next_eng;                                                                                           
$oldest_service[]=$longest_day_for_this_engineer;
	
                            }


array_multisort($oldest_service, $engineers_array);
$this->random_multisort($oldest_service, $engineers_array);



if ($oldest_service[0]==0){return $engineers_array[0];} else
{
	return $engineers_array[sizeof($engineers_array)-1];
}


return $longest_days_service_engineer;


/*
$r=rand(0,sizeof($engineers_array)-1);
return $engineers_array[$r];
*/


                                      }
                                    

private function random_multisort(&$primary_arr,&$secondary_arr){
	
array_push($primary_arr,"_one_extra_random_element");
	
$s=sizeof($primary_arr);
$start=0;$stop=0;
for ($counter=0;$counter<$s;$counter++){
	
$next=$primary_arr[$counter];

if ( @$hash["$next"]=='' ){


$hash["$next"]='Y';	
$stop=$counter-1;



$this->shuffle_elements($start,$stop,$primary_arr,$secondary_arr);



$start=$counter;

	
                        }	
                        
                        
	
                                       }
                                       
array_pop($primary_arr);                                       


	                                                           }
	                                                           
	                                                           
private function shuffle_elements($first_element,$last_element,&$arr1,&$arr2){
//50x of random switching between element ranges
for ($cycle=1;$cycle<=10;$cycle++){
$pick_1=rand($first_element,$last_element);
$pick_2=rand($first_element,$last_element);

$tmp=$arr2[$pick_1];
$arr2[$pick_1]=$arr2[$pick_2];
$arr2[$pick_2]=$tmp;

	
                                  }	
	
}
	
	
                                                              
	
};



$wheel=new SWOF(Array("Alvin","Alex","Catherine","Cecelia","Denker","Donothan","Garard","Gallifrey","Heather","Hannah"),"2018-12-17");


