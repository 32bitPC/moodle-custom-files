<?php
//For testing the AD server is work or not
$ldaphost="phuhunglife.com";
$ldapconn=ldap_connect($ldaphost);
 //rdn:relative distinguished name
$User="vitd0145";
$ldaprdn=$User."@".$ldaphost;
$ldappass="Cphl2021";
echo "ldapconn is ".$ldapconn."\n";    
ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
    
    //Reference：http://php.net/manual/en/function.ldap-bind.php
if ($ldapconn) {
        // binding to ldap server
        $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);
        // verify binding
        echo "ldapbind is ".$ldapbind."\n";
        if ($ldapbind) {
            echo "LDAP bind successful...";
            $ldap_base_dn = 'cn=users,dc=phuhunglife,dc=com';
            $search_filter = '(&(objectCategory=person)(samaccountname=*))';
            $attributes = array();
            $attributes[] = 'givenname';
            $attributes[] = 'mail';
            $attributes[] = 'samaccountname';
            $attributes[] = 'sn';
            $result = ldap_search($ldapconn, $ldap_base_dn, $search_filter, $attributes);
            print "ldap_search error: ".ldap_error($ldapconn) . '<br />';
            echo "result = ".$result."\n";
            if (false !== $result) {
                print "LDAP Search...<br />";
                $entries = ldap_get_entries($ldapconn, $result);
                echo "entries = ".$entries."\n";
                for ($x=0; $x<$entries['count']; $x++) {
                    if (!empty($entries[$x]['givenname'][0]) &&
                        !empty($entries[$x]['mail'][0]) &&
                        !empty($entries[$x]['samaccountname'][0]) &&
                        !empty($entries[$x]['sn'][0]) &&
                        'Shop' !== $entries[$x]['sn'][0] &&
                        'Account' !== $entries[$x]['sn'][0]) {
                        $ad_users[strtoupper(trim($entries[$x]['samaccountname'][0]))] = array('email' => strtolower(trim($entries[$x]['mail'][0])),'first_name' => trim($entries[$x]['givenname'][0]),'last_name' => trim($entries[$x]['sn'][0]));
                    }
                }
            }
            ldap_unbind($ldapconn); // Clean up after ourselves.
        } else {
            echo "LDAP bind failed...";
        }
        $message .= "Retrieved ". count($ad_users) ." Active Directory users\n";
        
        print $message;
        
        echo '<pre>';
        print_r($entries);
        echo '</pre>';
    }
?>
