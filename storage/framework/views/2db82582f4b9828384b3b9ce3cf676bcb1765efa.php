<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('vendors/select2/dist/css/select2.min.css')); ?>">
    <link rel="stylesheet"
          href="<?php echo e(asset('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')); ?>">
    <style>

        .select2 {
            width: 100% !important;
        }

        table.select2 {
            width: 100% !important;
        }

        .select2-dropdown--below {
            /*z-index: -1 !important;*/
        }

        /*#fleet-modal, .select2-dropdown{*/
        /*z-index: 10055 !important;*/
        /*}*/

        /*.modal-open,.modal,.fade,.in,.select2-dropdown {*/
        /*z-index: 0 !important;*/
        /*}*/

        /*.modal-open .select2-close-mask {*/
        /*z-index: 10055 !important;*/
        /*}*/

        .select2-container .select2-selection--multiple {
            -webkit-user-select: all;
            -moz-user-select: all;
            -ms-user-select: all;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Create Diesel Return
                    </h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <form id="form-horizontal form-label-left" class="form-data" method="post" action="#">
                        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                        <input type="hidden" id="user_id" value="<?php echo e($data['user_id']); ?>">
                        <input type="hidden" id="total_fuel" name="total_fuel" class="total_fuel"/>
                        
                        <h2 class="text-center text-danger msg hidden"></h2><br>
                        <div class="table-responsive">
                            <table class="table table-striped table-responsive table-bordered col-lg-12 col-md-12 col-sm-12 col-xs-12 table_parent"
                                   width="100%">
                                <tbody>
                                <tr>
                                    <td style="width: 20%;">
                                        <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Ticket Number<span
                                                    class="required text-danger">*</span></label>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                             style="padding-right: 0px !important;padding-left: 0px !important;">
                                            <select name="ticket_number" id="ticket_number" required
                                                    class="form-control select_ticket">
                                                <option value="">--Please Select--</option>
                                            </select>
                                        </div>

                                    </td>
                                    <td style="width: 15%;">
                                        <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Amount Fuel(L)
                                            <span class="required text-danger"></span>
                                        </label>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <input type="number" name="amount_fuel_credit" id="amount_fuel_credit"
                                                   class="form-control col-lg-12 col-md-12 col-sm-12 col-xs-12 amount_fuel_credit"
                                                   step="0.01" autofocus>
                                        </div>
                                    </td>

                                    <td style="width: 15%;">
                                        <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Lolo Amount($)
                                            <span class="required text-danger"></span>
                                        </label>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <input type="number" name="lolo_amount_credit" id="lolo_amount_credit"
                                                   class="form-control col-lg-12 col-md-12 col-sm-12 col-xs-12 lolo_amount_credit"
                                                   step="0.01">
                                        </div>
                                    </td>

                                    <td style="width: 15%;">
                                        <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">PayTrip Amount(៛)
                                            <span class="required text-danger"></span>
                                        </label>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <input type="number" name="paytrip_amount_credit" id="paytrip_amount_credit"
                                                   class="form-control col-lg-12 col-md-12 col-sm-12 col-xs-12 paytrip_amount_credit"
                                                   step="0.01">
                                        </div>
                                    </td>
                                    <td style="width: 40%;">
                                        <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Remark<span class="required text-danger">*</span>
                                        </label>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <input type="text"
                                                   class="form-control col-lg-12 col-md-12 col-sm-12 col-xs-12 remark_credit"
                                                   id="remark_credit" name="remark_credit">
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <button class="btn btn-danger btn-sm btn-cancel" type="reset">Cancel</button>
                                <button type="submit" class="btn btn-success btn-sm btn-save">Save</button>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="row reference_info"></div>
            </div>
        </div>
    </div>
    
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <?php echo $__env->make('pages.backend.js.credit.credit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('includes.master_backend', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>