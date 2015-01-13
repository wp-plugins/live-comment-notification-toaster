 function check_live_comments_bc()
    {
        
        var data = {
            'action': 'check_new_comments_ajax_toast'
        };
        jQuery.post(ajaxurl, data, function(response) {
           
             if(response!='-2' && response!='0' && response!='')
{
             var shortCutFunction = 1;
            var msg = 'abc';
            var title = 'title';
            var $showDuration = '1000';
            var $hideDuration = '5000';
            var $timeOut = jQuery('#default_toast_time').val();
            var $extendedTimeOut = '5000';
            var $showEasing = 'swing';
            var $hideEasing = 'swing';
            var $showMethod = 'fadeIn';
            var $hideMethod = "fadeOut";
            var toastIndex = 2;

            toastr.options = {
                closeButton:1,
                debug: 0,
                progressBar: 1,
                positionClass: 'toast-bottom-right',
                preventDuplicates: 0,
                onclick: null
            };
            
            if ($showDuration.length) {
                toastr.options.showDuration = $showDuration;
            }

            if ($hideDuration.length) {
                toastr.options.hideDuration = $hideDuration;
            }

            if ($timeOut.length) {
                toastr.options.timeOut = $timeOut;
            }

            if ($extendedTimeOut.length) {
                toastr.options.extendedTimeOut = $extendedTimeOut;
            }

            if ($showEasing.length) {
                toastr.options.showEasing = $showEasing;
            }

            if ($hideEasing.length) {
                toastr.options.hideEasing = $hideEasing;
            }

            if ($showMethod.length) {
                toastr.options.showMethod = $showMethod;
            }

            if ($hideMethod.length) {
                toastr.options.hideMethod = $hideMethod;
            }



            if (!msg) {
                msg = getMessage();
            }

            jQuery("#toastrOptions").text("Command: toastr["
                            + shortCutFunction
                            + "](\""
                            + msg
                            + (title ? "\", \"" + title : '')
                            + "\")\n\ntoastr.options = "
                            + JSON.stringify(toastr.options, null, 2)
            );
            response=response.slice(0, response.lastIndexOf("#####"));
            var ary=response.split("#####");
            for(var i=0;i<ary.length;i++)
            {
              Command: toastr["info"](ary[i]);  
            }
             
             
}
    
        });
    }