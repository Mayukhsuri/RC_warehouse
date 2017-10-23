<div class="column60">
    <div class="contentblock">
        <?php
     write_log(__FILE__,__LINE__);
            
            function loctypetext($type) {
                switch($type) {
                    case 'W': $output = 'Warehouse'; break;
                    case 'O': $output = 'Field Location'; break;
                    case 'C': $output = 'Courier'; break;
                    case 'T': $output = 'Temporary'; break;
                    case 'D': $output = 'Destruction'; break;
                    case 'P': $output = 'Pending'; break;
                    default: $output = 'Unknown';
                }
                return $output;
            }

          //  include('includes/connection.php');
            echo '<h2>Reprint Box Label</h2>' . PHP_EOL;
            echo '<div class="linklist">';
            echo '<a href="index.php?pid=74">Field Locations</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=75">Warehouse Locations</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=76">Boxes</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=77">Printfiles</a>';
            echo '</div>' . PHP_EOL;
            echo '<div class="spacer"></div>' . PHP_EOL;
            
            $form->displayForm('Reprint Label');

        ?>
    </div>
</div>
