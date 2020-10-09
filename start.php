<?php

require 'config/config.php';
require 'vendor/autoload.php';

use Models\Database;

//Initialize Illuminate Database Connection
new Database();

echo "test";