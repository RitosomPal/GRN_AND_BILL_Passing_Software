<?php if(isset($notify)) { ?>
    <script>
        $(function () {
            toastr.options = {
                "closeButton": true,
                "newestOnTop": true,
                "preventDuplicates": true
            }
            <?php if ($notify['success']) { ?>
                toastr.success('<?php echo $notify['msg'][0]; ?>')
            <?php } else { ?>
                toastr.error('<?php echo $notify['msg'][0]; ?>')
            <?php } ?>
        });
    </script>
<?php } ?>