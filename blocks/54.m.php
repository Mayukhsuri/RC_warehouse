<div class="column60">
    <div class="contentblock">
        
        <h2>Device Information</h2>
        <div class="spacer"></div><br/>
        <div class="datalist">
            <div class="dline"><div class="label">Device ID</div><div class="data"><?php echo $devidtext; ?></div></div>
            <div class="dline"><div class="label">Device Name</div><div class="data"><?php echo $devname; ?></div></div>
            <div class="dline"><div class="label">Application</div><div class="data"><?php echo $apptypetext; ?></div></div>
            <div class="dline"><div class="label">Date Activated</div><div class="data"><?php echo $adatetext; ?></div></div>
            <div class="dline"><div class="label">Current Status</div><div class="data"><?php echo $activetext; ?></div></div>
        </div>
        <div class="spacer"></div>
        
        <?php $form->displayForm('Update Device Status'); ?>
        
        <?php if(isset($_GET['upd'])) {echo '<script type="text/javascript">window.onload = alert("Device status updated.");</script>'; } ?>
    
    </div>
</div>
