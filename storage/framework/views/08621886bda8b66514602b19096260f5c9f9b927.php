<script>
    //for select2 of reason-list
    call_customer_script_select2();

    function call_customer_script_select2() {
        $(".select_customer").select2({
            placeholder: "--Please Select--",
            tags: true,
            ajax: {
                dataType: 'json',
                url: '<?php echo e(url('/admin/ticket/get_customer')); ?>',
                data: function (params) {
                    return {q: params.term}
                },
                processResults: function (data, params) {

                    var resData = [];
                    data.forEach(function (value) {
                        resData.push(value)
                    });

                    return {
                        results: $.map(resData, function (item) {
                            return {
                                text: item.CustomerName,
                                id: item.ID,
                            }
                        })
                    };
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            },
        });
    }

    //script for changing of customer
    $("body").on("change", ".select_customer", function () {

        var data_number = $(this).data("customer");
        var reason = $(".select_reason" + data_number + " option:selected").val();
        var destination = $(".select_destination" + data_number + " option:selected").val();

        if (reason == "") {
            $(".msg").text("Please Select Purpose").addClass("text-danger").removeClass("hidden");
            $(this).val('').trigger("change.select2");
            return false;
        } else if (destination == "") {
            $(".msg").text("Please Select Destination").addClass("text-danger").removeClass("hidden");
            $(this).val('').trigger("change.select2");
            return false;
        } else {
            return true;
        }
    });
</script>