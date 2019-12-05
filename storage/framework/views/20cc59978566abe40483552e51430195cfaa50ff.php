<script src="<?php echo e(asset('vendors/select2/dist/js/select2.min.js')); ?>"></script>
<script src="<?php echo e(asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')); ?>"></script>

<script type="text/javascript">

    //function for tab key
    var tabindex = 1;
    $('input,select,button,.btn-remove').each(function () {
        if (this.type != "hidden") {
            $(this).attr("tabindex", tabindex);
            tabindex++;
        }
    });

    $(".select_destinations,.select_reasons").select2();
    // https://stackoverflow.com/a/50535297/2782670
    $(document).on('focus', '.select2,input', function (e) {

        $(".swal2-container").addClass("hidden");
        $(".select2").find('.select2-selection__rendered').css({"background-color": "white"});
        $(this).find('.select2-selection__rendered').css({"background-color": "yellow"});

        if (e.originalEvent) {
            var s2element = $(this).siblings('select');
            s2element.select2('open');
            s2element.on('select2:closing', function (e) {
                s2element.select2('focus');
            });
        }

    });

    //this for get current date
    var current_date = new Date();

    //this function for add day
    function addDays(dateObj, numDays) {
        dateObj.setDate(dateObj.getDate() + numDays);
        return dateObj;
    }

    //this function to get expired date
    function get_expired_date(date) {

        var date = addDays(new Date(date), 2);

        var hours = date.getHours();
        var minutes = date.getMinutes();
        var seconds = date.getSeconds();
        var month = date.getMonth() + 1;

        var datestring = (((date.getDate() < 10) ? "0" + date.getDate() : date.getDate()) + "/" + ((month < 10) ? "0" + month : month)) + "/" + date.getFullYear();
        var timeString = "" + ((hours > 12) ? hours - 12 : hours);
        var zero = ((timeString > 9) ? "" : "0");
        timeString += ((minutes < 10) ? ":0" : ":") + minutes;
        timeString += ((seconds < 10) ? ":0" : ":") + seconds;
        timeString += (hours >= 12) ? " PM" : " AM";
        var fulltime = zero + timeString
        var fuledatetime = datestring + " " + fulltime;
        return fuledatetime;
    }

    //this function to get current date
    function get_current_date() {
        var date = new Date();
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var seconds = date.getSeconds();
        var month = date.getMonth() + 1;

        var datestring = (((date.getDate() < 10) ? "0" + date.getDate() : date.getDate()) + "/" + ((month < 10) ? "0" + month : month)) + "/" + date.getFullYear();
        var timeString = "" + ((hours > 12) ? hours - 12 : hours);
        var zero = ((timeString > 9) ? "" : "0");
        timeString += ((minutes < 10) ? ":0" : ":") + minutes;
        timeString += ((seconds < 10) ? ":0" : ":") + seconds;
        timeString += (hours >= 12) ? " PM" : " AM";
        var fulltime = zero + timeString
        var fuledatetime = datestring + " " + fulltime;
        return fuledatetime;
    }

    //for passing value into expired date and issue date input form
    // $('#expired_date_label').text(get_expired_date(current_date));
    // $('#issue_date_label').text(get_current_date());

    $('#issue_date').val(get_current_date());
    $('#expired_date').val(get_expired_date(current_date));

    $(".select_team_name,.select_mtpickup").select2();

    //script for note auto field
    $("body").on("keyup", ".note", function () {
        var value = $(this).val();
        var number = $(this).data("note");
        if (value != "") {
            var reason = $(".select_reason" + number + " option:selected").val();
            var destination = $(".select_destination" + number + " option:selected").val();

            if (reason == "") {
                $(".msg").text("Please Select Purpose").addClass("text-danger").removeClass("hidden");
                $(this).val('').trigger("change.select2");
                return false;
            } else if (destination == "") {
                $(".msg").text("Please Select Destination").addClass("text-danger").removeClass("hidden");
                $(this).val('').trigger("change.select2");
                return false;
            } else {
                if (value != "") {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: "<?php echo e(url('/admin/ticket/autocomplete')); ?>",
                        data: {
                            'value': value,
                            'number': number
                        },
                        success: function (response) {
                            if (response.con == 1) {
                                window.location = "<?php echo e(url('/admin/login')); ?>";
                            }

                            if (response.error == false) {
                                $(".note_list" + number).html(response.result).fadeIn();
                            }

                        }
                    });
                }
            }
        } else {
            $(".note_list" + number).html('').fadeOut();
        }
    });

    $("body").on("click", ".text_note_fuel", function () {
        var number = $(this).data("number");
        $(".note" + number).val($(this).text());
        $(".note_list" + number).fadeOut();
    });

    //script for note pay trip auto field
    $("body").on("keyup", ".note_paytrip", function () {
        var value = $(this).val();
        var number = $(this).data("note_paytrip");

        if (value != "") {

            var reason = $(".select_reason" + number + " option:selected").val();
            var destination = $(".select_destination" + number + " option:selected").val();

            if (reason == "") {
                $(".msg").text("Please Select Purpose").addClass("text-danger").removeClass("hidden");
                $(this).val('').trigger("change.select2");
                return false;
            } else if (destination == "") {
                $(".msg").text("Please Select Destination").addClass("text-danger").removeClass("hidden");
                $(this).val('').trigger("change.select2");
                return false;
            } else {
                if (value != "") {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: "<?php echo e(url('/admin/ticket/getnote_paytrip')); ?>",
                        data: {
                            'value': value,
                            'number': number
                        },
                        success: function (response) {
                            if (response.con == 1) {
                                window.location = "<?php echo e(url('/admin/login')); ?>";
                            }

                            if (response.error == false) {
                                $(".note_paytrip_list" + number).html(response.result).fadeIn();
                            }

                        }
                    });
                }
            }
        } else {
            $(".note_paytrip_list" + number).html('').fadeOut();
        }
    });

    $("body").on("click", ".text_note_pay_trip", function () {
        var number = $(this).data("number");
        $(".note_paytrip" + number).val($(this).text());
        $(".note_paytrip_list" + number).fadeOut();
    });

    //this script for on change addmore
    $("body").on("keyup blur", ".add_more", function (evt) {
        evt.preventDefault();

        var data_number = $(this).data("addmore");
        var reason = $(".select_reason" + data_number + " option:selected").val();
        var destination = $(".select_destination" + data_number + " option:selected").val();
        var value = $(this).val();

        if (reason == "") {
            $(".msg").text("Please Select Purpose").addClass("text-danger").removeClass("hidden");
            $(this).val('').trigger("change.select2");
            return false;
        } else if (destination == "") {
            $(".msg").text("Please Select Destination").addClass("text-danger").removeClass("hidden");
            $(this).val('').trigger("change.select2");
            return false;
        } else {
            var fuel = $(".fuel" + data_number).val();
            var diesel_return = $(".diesel_return_amount" + data_number).val();

            if (fuel == "") {
                fuel = 0;
            }

            if (diesel_return == "") {
                diesel_return = 0;
            }

            if (value != "") {
                value = value;
            } else {
                value = 0;
            }

            $(".total_amount" + data_number).val(parseFloat(fuel) + parseFloat(diesel_return) + parseFloat(value));
            $(".total_amount_original" + data_number).val(parseFloat(fuel) + parseFloat(value));

            var total = 0;
            $(".total_amount").each(function (key) {
                total += parseFloat($(this).val());
            });
            $(".total_fuel").text(total + " L");
            $(".total_amount_fuel").val(total);

            var total_original = 0;
            $(".total_amount_original").each(function (key) {
                if ($(this).val() != "") {
                    total_original += parseFloat($(this).val()) || 0;
                }
            });
            $(".total_all_amount_original").val(total_original);
        }

    });

    //this script for on change add_cut_paytrip
    $("body").on("keyup blur", ".add_cut_paytrip", function (evt) {
        evt.preventDefault();

        var number = $(this).data("add_cut_paytrip");
        var reason = $(".select_reason" + number + " option:selected").val();
        var destination = $(".select_destination" + number + " option:selected").val();
        var value = $(this).val();

        if (reason == "") {
            $(".msg").text("Please Select Purpose").addClass("text-danger").removeClass("hidden");
            $(this).val('').trigger("change.select2");
            return false;
        } else if (destination == "") {
            $(".msg").text("Please Select Destination").addClass("text-danger").removeClass("hidden");
            $(this).val('').trigger("change.select2");
            return false;
        } else {

            var pay_trip = $(".paytrip" + number).val();

            if (pay_trip == "") {
                pay_trip = 0;
            }

            if (value != "") {
                value = value;
            } else {
                value = 0;
            }

            $(".total_amount_paytrip" + number).val(parseFloat(pay_trip) + parseFloat(value));

            var total_all = 0;
            $(".total_amount_paytrip").each(function (key) {
                total_all += parseFloat($(this).val());
            });
            $(".total_pay_trip").html(formatNumber(total_all) + " &#6107;");
            $(".total_amount_pay_trip").val(total_all);
        }

    });

    //this script for on change add_cut_paytrip
    $("body").on("change", ".select_mtpickup", function (evt) {
        evt.preventDefault();

        var number = $(this).data("mtpickup");
        var reason = $(".select_reason" + number + " option:selected").val();
        var destination = $(".select_destination" + number + " option:selected").val();
        var amount = $(this).find(':selected').data('amount');
        var mtpickup_name = $(this).find(':selected').text();

        if (reason == "") {
            $(".msg").text("Please Select Purpose").addClass("text-danger").removeClass("hidden");
            $(this).val('').trigger("change.select2");
            return false;
        } else if (destination == "") {
            $(".msg").text("Please Select Destination").addClass("text-danger").removeClass("hidden");
            $(this).val('').trigger("change.select2");
            return false;
        } else {

            $(".lolo" + number).val(amount);
            $(".mtpickup_name" + number).val(mtpickup_name);

            var total_amount_lolo = 0;

            $(".lolo").each(function (key) {
                if ($(this).val() != "") {
                    total_amount_lolo += parseFloat($(this).val());
                }
            });

            $(".total_mtpickup").text("$ " + total_amount_lolo);
            $(".total_amount_mtpickup").val(total_amount_lolo);
        }

    });

    //this script for calling data clone of add form
    var i = 1;
    $("body").on("click", ".btn-add", function (evt) {
        evt.preventDefault();

        $.ajax({
            type: "post",
            url: "<?php echo e(url('admin/ticket/clone-data')); ?>",
            dataType: 'json',
            data: {
                'id': i
            },
            success: function (response) {

                $('.body-table-ticket').append(response.result);

                call_select2_container1();
                call_select2_container2();
                call_customer_script_select2();
                call_reason_script_select2();
                call_destination_script_select2();
                $(".select_mtpickup").select2();
                call_team_leader_script_select2();
            }
        });
        i++;
    });

    //script remove row of table
    $("body").on("click", ".btn-remove", function (evt) {
        evt.preventDefault();

        $(this).closest('.row-remove').remove();

        var total = 0;

        $(".total_amount").each(function (key) {
            if ($(this).val() != "") {
                total += parseFloat($(this).val());
            }
        });

        $(".total_fuel").text(total + " L");
        $(".total_amount_fuel").val(total);


        var total_amount_lolo = 0;

        $(".lolo").each(function (key) {
            if ($(this).val() != "") {
                total_amount_lolo += parseFloat($(this).val());
            }
        });

        $(".total_mtpickup").text("$ " + total_amount_lolo);
        $(".total_amount_mtpickup").val(total_amount_lolo);

        var total_all = 0;
        $(".total_amount_paytrip").each(function (key) {
            if ($(this).val() != "") {
                total_all += parseFloat($(this).val());
            }
        });

        $(".total_pay_trip").html(formatNumber(total_all) + " &#6107;");
        $(".total_amount_pay_trip").val(total_all);

        var total_original = 0;
        $(".total_amount_original").each(function (key) {
            if ($(this).val() != "") {
                total_original += parseFloat($(this).val()) || 0;
            }
        });
        $(".total_all_amount_original").val(total_original);

    });

    //this script is for saving data from form
    $("body").on("click", ".btn-save", function (evt) {
        evt.preventDefault();
        save_ticket();
    });

    //this script is for cancel
    $(".btn-cancel").click(function () {
        $(".msg").text('').addClass("hidden");
        clear();
    });

    //this function to clear data form
    function clear() {

        $(".reference_number,.fuel,.add_more,.paytrip,.add_cut_paytrip,.feet1,.feet2,.lolo,.diesel_return_amount,.total_amount,.total_amount_paytrip,.note,.note_paytrip,.total_amount_fuel,.total_all_amount_original,.total_amount_pay_trip,.total_amount_mtpickup,.fuel_add,.fuel_no_add,.fuel_added,.paytrip_id,.total_amount_original,.credit_note").val('');
        $(".total_fuel").val(0 + " L");
        $(".total_mtpickup").val("$ " + 0);
        $(".total_pay_trip").html(0 + " &#6107;");
        $(".fuel_content").addClass("hidden");
        $(".select_driver,.select_team_name,.select_mtpickup").val("").trigger('change.select2');
        call_fleet_script_select2();
        call_trailer_script_select2();
        call_reason_script_select2();
        call_destination_script_select2();
        call_customer_script_select2();
        call_select2_container1();
        call_select2_container2();
        call_team_leader_script_select2();
    }

    //this script to show infomation of ticket
    $("body").on("click", ".btn-detail", function (evt) {

        evt.preventDefault();

        var id = $(this).data("ticket_id");
        var number = $(this).data("number");

        var text = $(this).text();

        $.ajax({
            type: 'GET',
            url: '<?php echo e(url('/admin/credit/get_reference')); ?>/' + id,
            success: function (response) {
                $(".content-body-info").html(response.result);
            }
        });

        var msg = '';
        if (number == 1) {
            msg = "truck and driver";
        } else if (number == 2) {
            msg = "driver";
        } else if (number == 3) {
            msg = "truck";
        } else if (number == 4) {
            msg = "trailer";
        } else if (number == 5) {
            msg = "container";
        }
        call_notification("info", "This " + msg + " already had in ticket number: <a class='btn btn-success btn-sm btn-detail' data-ticket_id='" + id + "' data-number='" + number + "'>" + text + "</a>");

        $(".info_title").text("Information of reference number: " + text);
        $("#mdal_ticket_info").modal("show");

    });

    //this script for apply button
    var onclick = 0;
    $("body").on("click", ".btn-apply", function (evt) {
        evt.preventDefault();

        if ($(".select_reason select_reason0 option:selected").val() == "") {
            $(".msg").text("Please Select Purpose").addClass("text-danger").removeClass("hidden");
            return false;
        } else if ($(".select_destination0 option:selected").val() == "") {
            $(".msg").text("Please Select Destination").addClass("text-danger").removeClass("hidden");
            return false;
        }

        var id = $(this).data("id");
        var ticket_id = $(this).data("ticket");
        var remark = $(this).data("remark");
        var amount = $(this).data("amount");

        var credit_note = $(".credit_note").val();

        if (credit_note == "") {
            var credit_id = [];
        } else {
            var credit_id = [credit_note];
        }

        credit_id.push(id);
        var note = $(".note0").val();
        var diesel = $(".diesel_return_amount0").val();


        if (note != "") {
            note += ", " + remark;
        } else {
            note += remark;
        }

        if (diesel != "") {
            diesel = ((-1) * diesel + amount);
        } else {
            diesel += amount;
        }

        diesel = (-1) * diesel;

        $(".note0").val(note);

        $(".diesel_return_amount0").val(parseFloat(diesel));

        $(".credit_note").val(credit_id);

        var rowCount = $('.data-credit tr').length

        if (rowCount > 1) {
            $(".credit-remove" + id).remove();
        } else {
            $(".credit-remove" + id).parents(".div-credit").addClass("hidden");
        }

        var total_fuel = $(".fuel0").val();
        var diesel_return = $(".diesel_return_amount0").val();
        var add_more = $(".add_more0").val();

        if (total_fuel == "") {
            total_fuel = 0;
        }

        if (diesel_return == "") {
            diesel_return = 0;
        }

        if (add_more == "") {
            add_more = 0;
        }

        $(".total_amount0").val(parseFloat(total_fuel) + parseFloat(diesel_return) + parseFloat(add_more));

        var total_all = 0;
        $(".total_amount").each(function (key) {
            total_all += parseFloat($(this).val());
        });
        $(".total_fuel").text(total_all + " L");
        $(".total_amount_fuel").val(total_all);

        $(".msg").addClass("hidden").removeClass("text-danger").text('');

        onclick++;
    });

    //ctrl +s
    $.ctrl = function (key, callback, args) {
        var isCtrl = false;
        $(document).keydown(function (e) {
            if (!args) args = []; // IE barks when args is null

            if (e.ctrlKey) isCtrl = true;
            if (e.keyCode == key.charCodeAt(0) && isCtrl) {
                callback.apply(this, args);
                return false;
            }
        }).keyup(function (e) {
            if (e.ctrlKey) isCtrl = false;
        });
    };
    $.ctrl('S', function () {
        save_ticket();
    });

    //this function for save function
    function save_ticket() {

        if (!$(".btn-save").hasClass('disabled')) {

            $(".btn-save").addClass("disabled");

            var issue_date = $("#issue_date").val();
            var expired_date = $("#expired_date").val();
            var driver = $(".select_driver option:selected").val();
            var select_plate_number = $(".select_plate_number option:selected").val();
            var trailer = $(".select_trailer option:selected").val();

            var reason = $(".select_reason option:selected").val();
            var destination = $(".select_destination option:selected").val();

            var select_customer = $(".select_customer option:selected").val();
            var select_container1 = $(".select_container1 option:selected").val();
            var select_mtpickup = $(".select_mtpickup option:selected").val();

            var total = $("#total_amount_fuel").val();
            var paytrip = $("#total_amount_pay_trip").val();
            var lolo = $("#total_amount_mtpickup").val();
            var feet1 = $(".feet1").val();

            if (issue_date == "") {
                $(".msg").text("Your Issue Date is blank. Please Fill it.").removeClass("hidden").addClass("text-danger");
                $(".btn-save").removeClass("disabled");
            } else if (expired_date == "") {
                $(".msg").text("Your Expired Date is blank. Please Fill it.").removeClass("hidden").addClass("text-danger");
                $(".btn-save").removeClass("disabled");
            } else if (select_plate_number == "") {
                $(".msg").text("Please Select Plate Number").removeClass("hidden").addClass("text-danger");
                $(".btn-save").removeClass("disabled");
            } else if (driver == "") {
                $(".msg").text("Please Select Driver.").removeClass("hidden").addClass("text-danger");
                $(".btn-save").removeClass("disabled");
            } else if (trailer == "") {
                $(".msg").text("Please Select Trailer.").removeClass("hidden").addClass("text-danger");
                $(".btn-save").removeClass("disabled");
            } else if (reason == "") {
                $(".msg").text("Please Select Purpose").removeClass("hidden").addClass("text-danger");
                $(".btn-save").removeClass("disabled");
            } else if (destination == "") {
                $(".msg").text("Please Select Destination").removeClass("hidden").addClass("text-danger");
                $(".btn-save").removeClass("disabled");
            } else if (select_customer == "") {
                $(".msg").text("Please select Customer Name.").removeClass("hidden").addClass("text-danger");
                $(".btn-save").removeClass("disabled");
            } else if ((select_container1 == "") && (select_mtpickup == "")) {
                $(".msg").text("Please select Container Number.").removeClass("hidden").addClass("text-danger");
                $(".btn-save").removeClass("disabled");
            }
            else if (((total == "") || (total <= 0)) && ((paytrip == "") || (paytrip <= 0)) && ((lolo == "") || (lolo <= 0))) {
                $(".msg").text("The ticket can not create without fuel, paytrip or lolo.").removeClass("hidden").addClass("text-danger");
                $(".btn-save").removeClass("disabled");
            } else if ((feet1 == "") && (select_mtpickup == "")) {
                $(".msg").text("Please Enter Feet of container.").removeClass("hidden").addClass("text-danger");
                $(".btn-save").removeClass("disabled");
            } else {

                $.ajax({
                    url: "<?php echo e(url('admin/ticket/save')); ?>",
                    type: 'POST',
                    data: $('.form-ticket').serialize(),
                    success: function (response) {
                        $(".image_more_loading").addClass("hidden");
                        if (response.error == 0) {
                            printLabelBarcode(response.Label);
                            setTimeout(function () {
                                window.location = "<?php echo e(url('admin/ticket/create')); ?>";
                            }, 300);
                        } else {
                            $(".msg").text(response.error).removeClass("hidden").addClass("text-danger");
                            $(".btn-save").removeClass("disabled");
                        }
                    },
                    beforeSend: function () {
                        $(".image_more_loading").removeClass("hidden");
                    },
                });
            }
        }
    }

    $("body").on("click", ".btn_copy_one", function (evt) {
        var number = $(this).data("number");
        var text = $(".select_container1" + number + " option:selected").text();

        if (text !== "") {

            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(text).select();
            document.execCommand("copy");
            $temp.remove();
        }

    });

    $("body").on("click", ".btn_copy_two", function (evt) {
        var number = $(this).data("number");
        var text = $(".select_container2" + number + " option:selected").text();

        if (text !== "") {

            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(text).select();
            document.execCommand("copy");
            $temp.remove();
        }
    });

</script>

<?php echo $__env->make('pages.backend.js.ticket.fleet_script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('pages.backend.js.ticket.driver_script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('pages.backend.js.ticket.trailer_script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('pages.backend.js.ticket.reason_script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('pages.backend.js.ticket.destination_script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('pages.backend.js.ticket.customer_script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('pages.backend.js.ticket.container_script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('pages.backend.js.ticket.team_leader', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

