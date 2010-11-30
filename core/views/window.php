<form id="trigger_form">

<textarea name="trigger_content" id="trigger_content">
<?php foreach( $context as $item ): echo $item . " : "; endforeach; ?>

</textarea>

<div id="trigger_target"></div>

</form>