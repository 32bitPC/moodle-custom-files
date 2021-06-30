$array_username_sql = "
        select firstname from MOF where firstname = N'Hà'
";
            
            $users = $DB->get_records_sql($array_username_sql,array());
            foreach($users as $user){
                $user_id= $user->firstname;
            }
            if(is_null($user_id)){
                echo "not exist <br>";
                $insert = "
insert into MOF (firstname) values (N'Hà')";
                $DB->execute($insert,array());
            }
            echo "user = ".$user_id;
            
