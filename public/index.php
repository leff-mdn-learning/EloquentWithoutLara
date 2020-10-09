<?php

use Controllers\Users;

require '../start.php';

$user = Users::create_user("user1", "user1@example.com", "user1_pass");
