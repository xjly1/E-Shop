<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$autoload['packages'] = array();
$autoload['libraries'] = array(
    'database',
    'session',
    'loop',
    'ShoppingCart',
    'Language',
    'SendMail'
);
$autoload['drivers'] = array();
$autoload['helper'] = array(
    'overwrite_functions',
    'url',
    'language',
    'text',
    'cookie',
    'getTextualPages',
    'mb_ucfirst',
    'purchase_steps',
    'cleanreferral',
    'except_letters',
    'file',
    'pagination',
    'currencies',
    'rcopy',
    'rrmdir',
    'rreadDir',
    'savefile'
);
$autoload['config'] = array();
$autoload['language'] = array();
$autoload['model'] = array('Public_model', 'Home_admin_model');