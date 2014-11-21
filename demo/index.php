<?php
/*
 * Simple demo of ActiveAuth two-factor authentication
 *
 * Simple login form with hardcoded username/password
 *
 * Username: demo
 * Password: qwerty
 */

//Include main class of two-factor authentication ActiveAuth.php
include('../ActiveAuth.php');

//You have to generate unique key with length of 40 for your application
/*
 * Example how you can do it
 * $aKey = null;
 * for ($i=0; $i<40; $i++) {
 *   $aKey .= dechex(mt_rand(0, 15));
 * }
 */
define('AKEY', "bf66b9d9a440bf24d8230ecf07adb027f7bc065a");

/*
 * IKEY, SKEY, HOST and IACCOUNT should come from your ActiveAuth.me account
 * on the integrations page. It's recommended to keep this keys outside the web root
 * directory of your production application for security reasons
 */
define('IKEY', ""); //integration key
define('SKEY', ""); //secret key
define('HOST', ""); //api hostname
define('IACCOUNT', ""); //integration account

/*
 * ActiveAuth class object
 */
$activeAuth = new ActiveAuth();

//Info
echo "<h1>ActiveAuth Demo</h1>";
echo "<p>Username: demo </p><p>Password: qwerty </p><hr>";

/*
 * Step 1
 * Ordinary authentication
 * verify username and password
 * if they are ok, then start secondary authentication
 * by loading up ActiveAuth iframe
 */
if(isset($_POST['username']) && isset($_POST['password'])){
    if($_POST['username'] == 'demo' && $_POST['password'] == 'qwerty') {
        //login is ok, run secondary auth
        //generate secret request and then load up ActiveAuth javascript and iframe
        $secret = $activeAuth->sign(IACCOUNT, IKEY, SKEY, AKEY);
        ?>
        <iframe src="" id="acaframe" style='width: 40%;height: 40%;border: none;'></iframe>
        <script type="text/javascript">
            var ACASecret = '<?= $secret ?>';
            var ACAServer = '<?= HOST ?>';
            var ACAAccount = '<?= IACCOUNT ?>';
            var ACAAction = "";
        </script>
        <script type="text/javascript" src="../js/activeauth.js"></script>
    <?php
    }
}

/*
 * Step 2
 * The secondary authentication
 * Check for POST param from ActiveAuth
 */
else if (isset($_POST['2fa-verify'])) {
    //create ActiveAuth object and call verify method
    $response = $activeAuth->verify($_POST['2fa-verify'], SKEY, AKEY);

    //verify response and log in user
    //make sure that this response does not return NULL
    //if it is NOT NULL then it will return a account email address
    //you can then set any cookies/session data and complete the login process
    if ($response != NULL) {

        //your additional login process here

        echo 'Hello, ' . $response . '<br>';
        echo 'Your are logged!';
    }
}

/* Step 3:
 * simple login form
 */
else {
    echo "<form action='index.php' method='post'>";
    echo "<p>User: <input type='text' name='username' /></p>";
    echo "<p>Pass: <input type='password' name='password' /></p>";
    echo "<input type='submit' value='Submit' />";
    echo "</form>";
}
