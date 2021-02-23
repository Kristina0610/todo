<?php 

$projects = getProjects();
//$tags = getTags();

$tags = getTagAndCount();

$first_letters = getFirstLetterTag($tags);
//dump($first_letters);
include ("../templates/index.phtml");