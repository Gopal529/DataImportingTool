<?php
//load the database configuration file
include 'dbConfig.php';

if(isset($_POST['importSubmit'])){
    
    //validate whether uploaded file is a csv file
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'],$csvMimes)){
        if(is_uploaded_file($_FILES['file']['tmp_name'])){
            
            //open uploaded csv file with read only mode
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
            
            //skip first line
            fgetcsv($csvFile);
            
            //parse data from csv file line by line
            while(($line = fgetcsv($csvFile)) !== FALSE){

            $a=0; 
           $prevQuery = "SELECT * FROM mergedata WHERE id = '".$line[0]."'";
           $prevResult = $db->query($prevQuery);
                if($prevResult->num_rows > 0  ){                   
                    $row=$prevResult->fetch_assoc();
                                          
                    if($row['email']=="")
                    {   $db->query("UPDATE mergedata SET email ='".$line[2]."' WHERE id='".$line[0]."' AND (Name='".$line[1]."' OR Name='' OR '".$line[1]."'='') AND (phone='".$line[3]."' OR phone='' OR '".$line[3]."'='') AND (City='".$line[4]."' OR City='' OR '".$line[4]."'='')");$a++;}
                    if($row['Name']=="")
                    {   $db->query("UPDATE mergedata SET Name ='".$line[1]."'WHERE id='".$line[0]."'AND (email='".$line[2]."' OR email='' OR '".$line[2]."'='') AND (phone='".$line[3]."' OR phone='' OR '".$line[3]."'='') AND (City='".$line[4]."' OR City='' OR '".$line[4]."'='' )");$a++;}
                    if($row['phone']=="")
                       {   $db->query("UPDATE mergedata SET phone ='".$line[3]."'WHERE id='".$line[0]."'AND (Name='".$line[1]."' OR Name='' OR '".$line[1]."'='') AND (email='".$line[2]."' OR email='' OR '".$line[2]."'='')  AND (City='".$line[4]."' OR City='' OR '".$line[4]."'='')" );$a++;}
                    if($row['City']=="")
                       {   $db->query("UPDATE mergedata SET City ='".$line[4]."'WHERE id='".$line[0]."'AND (Name='".$line[1]."' OR Name='' OR '".$line[1]."'='') AND (email='".$line[2]."' OR email='' OR '".$line[3]."'='') AND (City='".$line[4]."' OR City='' OR '".$line[4]."'='')" );$a++;}
                      
}


                else{
                    //insert member data into database
                    $db->query("INSERT INTO mergedata(id,name, email, phone, city) VALUES ('".$line[0]."','".$line[1]."','".$line[2]."','".$line[3]."','".$line[4]."')");$a++;
                }
if($a==0){
$db->query("INSERT INTO conflictdata(id,name, email, phone, city) VALUES ('".$line[0]."','".$line[1]."','".$line[2]."','".$line[3]."','".$line[4]."')");
}
       }
            
                
            
            //close opened csv file
            fclose($csvFile);

            $qstring = '?status=succ';
        }else{
            $qstring = '?status=err';
        }
    }else{
        $qstring = '?status=invalid_file';
    }
}

//redirect to the listing page
header("Location: index.php".$qstring);
