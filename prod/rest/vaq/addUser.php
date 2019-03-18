#!/usr/bin/php
<?php
    $dir = dirname(__FILE__);
    $path = "$dir/data/users"; // The path to the users file.
    require_once("$dir/User.php");

    if ($argc < 4) {
?>
    Use this script from the command line to add a new user.

    Usage:
    <?php echo $argv[0]; ?> <username> <password> <legalOrigin...>

    <username>      The username for the new user.
    <password>      The password for the new user.
    <legalOrigins>  A list of origins the new user shall be able to access.
        Possible options are: CSG, CSG and Friends, BS 1 Ingolstadt, TUM, PROSIS, Beccis Grundschule, THI.
<?php
} else {
    $name = $argv[1];
    $pwd = $argv[2];
    echo "Username: $name\nPassword: $pwd\nOrigins:\n";

    $origins = [];
    $i = 3;
    do {
        echo "\t\"$argv[$i]\"\n";
        array_push($origins, $argv[$i]);
        $i++;
    } while (isset($argv[$i]));

    echo "\nAdd user with information above? (y/N) ";
    $handle = fopen("php://stdin", "r");
    $input = fgets($handle);
    if (trim($input) != 'y')
        die("Aborting!\n");

    $duplicate = false;
    $lines = file($path, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        $u = new User($line);
        if ($name == $u->getName())
            $duplicate = true;
    }
    if ($duplicate) {
        echo "\nA user with this username already exists.\nDo you want to override it? (y/N) ";
        $input = fgets($handle);
        if (trim($input) != 'y')
            die("Aborting!\n");

        foreach ($lines as $i => $line) {
            $u = new User($line);
            if ($name == $u->getName()) {
                $lines[$i] = json_encode(new User(false, $name, $pwd, $origins));
                break;
            }
        }
    } else {
        $lines[count($lines)] = json_encode(new User(false, $name, $pwd, $origins));
    }

    file_put_contents($path, implode(PHP_EOL, $lines));
}
?>

Done!
