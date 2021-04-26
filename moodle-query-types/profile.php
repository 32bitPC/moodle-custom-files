$sql = "SELECT *
          FROM {user}
         WHERE id = 240 ";
$records = $DB->get_record_sql($sql, array());

$records = $DB->get_record('user', array('id'=> 240 ), '*', MUST_EXIST);
