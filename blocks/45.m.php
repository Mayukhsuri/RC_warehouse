<?php

    if($checktask == 1) {
        
        echo '<div class="column60"><div class="contentblock">';
        echo '<h2>RC Boxes</h2>';
        echo '<div class="buttonleft"><a href="index.php?pid=45&lg=1">Generate Labels</a></div>';
        echo '<div class="buttonleft"><a href="index.php?pid=46">Finalize Boxes</a></div>';
        echo '</div></div>';
        
    } else {
        
        echo '<div class="column60"><div class="contentblock">';
        $form->displayForm('Generate Labels');
        echo '</div></div>';
        
    }

?>
