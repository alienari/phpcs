<?php

// correct

if (5 === 3) {
	echo 'foo' . 'bar';
} else {

}

foreach ($x as $y) {

}

array(

);

switch ($i) {

}

function test(& $x) {

}

if ($adjadsaljdaslllkhdskjadhasd
	&& $jdsaldhashdlkasdasd) {

}

$xml =
'<?xml version="1.0" encoding="UTF-8" ?>
<foobar>
	<foo>text</foo>
	<bar>text</bar>
	<empty/>
</foobar>';

// wrong

if(5 ===3) {
	echo 'foo' . 'bar';
}else {

}

foreach($x as $y) {

}

array (

);

switch($i) {

}