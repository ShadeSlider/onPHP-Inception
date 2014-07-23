<?php
function mp($var)
{
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}

function vi($var)
{
	mp($var);
	exit;
}

function mppp($var)
{
	mp($var);
	echo "\n\n";
}

function viii($var)
{
	mppp($var);
	exit;
}



function vd($var)
{
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
	exit;
}
function vdd($var)
{
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
}


function mpcli($var, $sep = '!=====')
{
	echo "\n\n" . $sep . "\n";
	print_r($var);
	echo "\n" . strrev($sep) . "\n\n";
}


function vdcli($var, $sep = '!=====')
{
	echo "\n\n" . $sep . "\n";
	var_dump($var);
	echo "\n" . strrev($sep) . "\n\n";
}


function vdvcli($var, $sep = '!=====')
{
	vdcli($var);
	exit;
}


function vicli($var)
{
	mpcli($var);
	exit;
}