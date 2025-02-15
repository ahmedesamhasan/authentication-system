<?php
require_once __DIR__ . '/vendor/autoload.php';

use User\Aurthenticationsystem\layout\Header;
use User\Aurthenticationsystem\layout\Footer;

Header::render();
?>

<h1>hello from index</h1>

<?php
Footer::render();