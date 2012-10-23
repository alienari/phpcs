<?php

throw new \Exception();
throw new \Collabim\Exception();

try {

} catch (\Exception $e) {

}

try {

} catch (\Collabim\Exception $e) {

}

function throwException()
{

}

$this->throwException = FALSE;
$this->throwException('foo');

throw new Exception();
throw new Collabim\Exception();

try {

} catch (Exception $e) {

}

try {

} catch (Collabim\Exception $e) {

}
