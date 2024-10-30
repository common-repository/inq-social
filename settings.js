jQuery(document).ready(function ($) {
    if ($("#inqsocial_website_id")) {
        jQuery("#wpbody-content .button-primary").click(function (event) {
            var source_value = $("#inqsocial_website_id").val();
            if (source_value.trim().length === 36) {
                var pattern = new RegExp("^[a-z0-9\-]*$");
                var test_result = pattern.test(source_value);
                if (!test_result) {
                    event.preventDefault();
                    alert("Please enter a valid InQ.Social Website Id to continue. It can be obtained from your website's dashboard in InQ.Social account.");
                }
            } else if ((source_value.trim().length < 36 || source_value.trim().length > 36) && source_value.trim().length !== 0) {
                event.preventDefault();
                alert("Please enter a valid InQ.Social Website Id to continue. It can be obtained from your website's dashboard in InQ.Social account.");
            }
        });
    }
});