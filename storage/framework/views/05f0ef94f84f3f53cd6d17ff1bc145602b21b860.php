<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-datetimepicker.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('vendors/select2/dist/css/select2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/buttons.dataTables.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('vendors/dataTables.fixedColumns/fixedColumns.bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/colReorder.dataTables.min.css')); ?>">
    <style>
        .select2 {
            width: 100% !important;
        }

        .content-form {
            width: 100%;
            padding: 10px 17px;
            display: inline-block;
            background: #fff;
            border: 2px solid #E6E9ED;
            -webkit-column-break-inside: avoid;
            -moz-column-break-inside: avoid;
            column-break-inside: avoid;
            opacity: 1;
            transition: all .2s ease;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Request List</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 content-form">
                        <form id="form-horizontal form-label-left" class="form-data" method="post"
                              action="<?php echo e(url('admin/request/list')); ?>" autocomplete="off">
                            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                            <input type="hidden" name="request_all_id_no_return" id="request_all_id_no_return"
                                   class="request_all_id_no_return">

                            <div class="table-responsive">
                                <table class="table table-striped table-responsive table-bordered col-lg-12 col-md-12 col-sm-12 col-xs-12 table_parent"
                                       width="100%">
                                    <tbody>
                                    <tr>
                                        <td style="width: 16%;">
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">From Date</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <input type="text" class="form-control has-feedback-left"
                                                       name="from_date"
                                                       id="from_date" placeholder="From Date"
                                                       aria-describedby="inputSuccess2Status3"
                                                       value="<?php echo e($data['from_date']); ?>"
                                                       style="height: 37px; !important;">
                                                <span class="fa fa-calendar-o form-control-feedback left"
                                                      aria-hidden="true"
                                                      style="margin-left: -7% !important; margin-top: 5px !important;"></span>
                                                <span id="inputSuccess2Status3" class="sr-only">(success)</span>
                                            </div>

                                        </td>
                                        <td style="width: 16%;">
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">To Date</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <input type="text" class="form-control has-feedback-left" name="to_date"
                                                       id="to_date" placeholder="To Date"
                                                       style="height: 37px; !important;"
                                                       aria-describedby="inputSuccess2Status3"
                                                       value="<?php echo e($data['to_date']); ?>">
                                                <span class="fa fa-calendar-o form-control-feedback left"
                                                      aria-hidden="true"
                                                      style="margin-left: -7% !important; margin-top: 5px !important;"></span>
                                                <span id="inputSuccess2Status3" class="sr-only">(success)</span>
                                            </div>

                                        </td>
                                        <td style="width: 16%;">
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Reference
                                                N<sup>0</sup></label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="reference_number" id="reference_number"
                                                        class="js-example-basic-single js-states form-control select_reference_number">
                                                    <option value="<?php echo e($data['ref_id']); ?>"><?php echo e($data['ref_name']); ?></option>

                                                </select>
                                                <input type="hidden" name="reference_name"
                                                       value="<?php echo e($data['ref_name']); ?>"
                                                       id="reference_name">
                                            </div>

                                        </td>
                                        <td style="width: 16%;">
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Supervisor</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="supervisor_id" id="supervisor_id"
                                                        class="js-example-basic-single js-states form-control select_supervisor_id">
                                                    <option value="<?php echo e($data['super_id']); ?>"><?php echo e($data['super_name']); ?></option>

                                                </select>
                                                <input type="hidden" name="supervisor_name"
                                                       value="<?php echo e($data['super_name']); ?>"
                                                       id="supervisor_name">
                                            </div>

                                        </td>
                                        <td style="width: 16%;">
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Truck
                                                N<sup>0</sup></label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="plate_number" id="plate_number"
                                                        class="js-example-basic-single js-states form-control plate_number-list">
                                                    <option value="<?php echo e($data['fleet_id']); ?>"><?php echo e($data['fleet_name']); ?></option>

                                                </select>
                                                <input type="hidden" name="fleet_name" value="<?php echo e($data['fleet_name']); ?>"
                                                       id="fleet_name">
                                            </div>

                                        </td>
                                        <td style="width: 16%;">
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Driver Name</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="driver_id" id="driver_id"
                                                        class="js-example-basic-single js-states form-control driver-list">
                                                    <option value="<?php echo e($data['driver_id']); ?>"><?php echo e($data['driver_name']); ?></option>

                                                </select>
                                                <input type="hidden" name="driver_name"
                                                       value="<?php echo e($data['driver_name']); ?>"
                                                       id="driver_name">
                                            </div>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">User</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="username" id="username"
                                                        class="js-example-basic-single js-states form-control username-list">
                                                    <option value="-1" <?php echo e(($data['user_id']==-1?'selected':'')); ?>>All
                                                    </option>
                                                    <?php if($data['id_user']): ?>
                                                        <?php $__currentLoopData = $data['data_user']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $users): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($users->ID); ?>" <?php echo e(($data['user_id']==$users->ID?'selected':'')); ?>><?php echo e($users->DisplayName); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>

                                        </td>
                                        <td>
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Trailer N<sup>0</sup></label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="trailer_number" id="trailer_number"
                                                        class="js-example-basic-single js-states form-control trailer_number-list">
                                                    <option value="<?php echo e($data['trailer_id']); ?>"><?php echo e($data['trailer_name']); ?></option>

                                                </select>
                                                <input type="hidden" value="<?php echo e($data['trailer_name']); ?>"
                                                       name="trailer_name"
                                                       id="trailer_name">
                                            </div>

                                        </td>
                                        <td>
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Purpose</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="purpose" id="purpose"
                                                        class="js-example-basic-single js-states form-control purpose-list">
                                                    <option value="<?php echo e($data['purpose_id']); ?>"><?php echo e($data['purpose_name']); ?></option>

                                                </select>
                                                <input type="hidden" value="<?php echo e($data['purpose_name']); ?>"
                                                       name="purpose_name"
                                                       id="purpose_name">
                                            </div>

                                        </td>
                                        <td>
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Destination</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="destination" id="destination"
                                                        class="js-example-basic-single js-states form-control destination-list">
                                                    <option value="<?php echo e($data['destination_id']); ?>"><?php echo e($data['destination_name']); ?></option>

                                                </select>
                                                <input type="hidden" name="destination_name"
                                                       value="<?php echo e($data['destination_name']); ?>" id="destination_name">
                                            </div>

                                        </td>
                                        <td>
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Request N<sup>0</sup></label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="request_id" id="request_id"
                                                        class="js-example-basic-single js-states form-control request-list">
                                                    <option value="<?php echo e($data['request_id']); ?>"><?php echo e($data['request_number']); ?></option>

                                                </select>
                                                <input type="hidden" value="<?php echo e($data['request_number']); ?>"
                                                       name="request_name"
                                                       id="request_name">
                                            </div>

                                        </td>
                                        <td>
                                            <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Status</label>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 style="padding-right: 0px !important;padding-left: 0px !important;">
                                                <select name="status" id="status"
                                                        class="js-example-basic-single js-states form-control status-list">
                                                    <option value="-1" <?php echo e(($data['status_id']=='-1'?'selected':'')); ?>>
                                                        All
                                                    </option>
                                                    <?php
                                                    $arr = [
                                                        "0" => "New",
                                                        "1" => "Used",
                                                        "2" => "Cancel",
                                                        "3" => "Pending"
                                                    ];
                                                    ?>
                                                    <?php $__currentLoopData = $arr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($key); ?>" <?php echo e(($key==$data['status_id']?'selected':'')); ?>><?php echo e($value); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>

                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-lg-5 col-md-9 col-sm-12 col-xs-12 form-group">
                                        <label for="button"
                                               class="control-label col-lg-1 col-md-1 col-sm-6 col-xs-12 hidden-lg hidden-md"></label>
                                        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                                            <label for="button">
                                                <button type="submit" class="btn btn-primary btn-sm"><i
                                                            class="fa fa-search"></i> Filter
                                                </button>

                                                <?php if($data['id_list']): ?>
                                                    <a href="<?php echo e(url('admin/request/export')); ?>"
                                                       class="btn btn-success btn-sm">
                                                        <i class="fa fa-file-excel-o"></i>
                                                        Export</a>
                                                <?php endif; ?>

                                                <?php if($data['search']): ?>
                                                    <a href="<?php echo e(url('admin/request/list')); ?>"
                                                       class="btn btn-danger btn-sm">Clear</a>
                                                <?php endif; ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table id="request-list-table" class="table table-striped table-bordered" cellspacing="0"
                               width="100%">
                            <thead>
                            <tr>
                                <th style="text-align: center !important; width: 5%;">
                                    <label for="">
                                        <?php if($data['ticket']): ?>
                                            <button class="btn btn-success btn-sm btn-request">
                                                Create Ticket
                                            </button>
                                        <?php endif; ?>

                                    </label>
                                </th>
                                <th>Request N<sup>0</sup></th>
                                <th>Created</th>
                                <th>User</th>
                                <th>Reference N<sup>0</sup></th>
                                <th>Supervisor</th>
                                <th>Truck</th>
                                <th>Driver</th>
                                <th>Team</th>
                                <th>Trailer</th>
                                <th>Purpose</th>
                                <th>Destination</th>
                                <th>Fuel</th>
                                <th>Add/Cut</th>
                                <th>Total Fuel</th>
                                <th>Customer</th>
                                <th>Container1</th>
                                <th>Feet1</th>
                                <th>Container2</th>
                                <th>Feet2</th>
                                <th>Note</th>
                                <th>Status</th>
                                <th>Remark</th>
                                <th>Created Ticket</th>
                                <th>Ticket N<sup>0</sup></th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody class="data-show">

                            </tbody>
                        </table>
                    </div>
                    <div>
                        <span id="all"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_new_ticket" class="modal fade">
        <div class="modal-dialog" style="width: 70% !important; margin-left: 16% !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-form-new" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h3 class="modal-title text-center new_ticket_title"></h3>
                </div>
                <div class="modal-body">
                    <div class="row content-body-new" id="data-new">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger close-form-new" data-dismiss="modal">Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_div_credit" class="modal fade">
        <div class="modal-dialog" style="width: 70% !important; margin-left: 16% !important;">
            <div class="modal-content">
                <form id="form-horizontal form-label-left" class="form-data-create-ticket-request" method="post"
                      action="#">
                    <div class="modal-header">
                        <button type="button" class="close close-form-div_credit" data-dismiss="modal"
                                aria-hidden="true">
                            &times;
                        </button>
                        <h3 class="modal-title text-center div_credit_title"></h3>
                    </div>
                    <div class="modal-body">
                        <div class="row content-body-credit" id="data-div_credit">

                            <h5 class="message text-danger hidden text-center"></h5>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label class="control-label col-lg-2 col-md-2 col-sm-4 col-xs-8"
                                       for="total_fuel" style="text-align: left;">Total Fuel:
                                </label>
                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                    <input type="text" readonly class="form-control total_fuel" name="total_fuel"
                                           id="total_fuel">
                                    <input type="hidden" name="request_all_id" id="request_all_id"
                                           class="request_all_id">
                                    <input type="hidden" name="original_fuel" id="original_fuel" class="original_fuel">
                                    <input type="hidden" name="credit_all_id" id="credit_all_id" class="credit_all_id">
                                    <input type="hidden" name="diesel_return_amount" id="diesel_return_amount"
                                           class="diesel_return_amount">
                                    <input type="hidden" name="diesel_return_note" id="diesel_return_note"
                                           class="diesel_return_note">
                                    <input type="hidden" name="ticket_amount_fuel" id="ticket_amount_fuel"
                                           class="ticket_amount_fuel">
                                </div>
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <table class="table table-responsive table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Plate Number</th>
                                        <th>Driver</th>
                                        <th>Diesel Return Number</th>
                                        <th>Diesel Return Amount</th>
                                        <th>Remark</th>
                                        <th>Apply</th>
                                    </tr>
                                    </thead>
                                    <tbody class="data-credit">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm btn_save_create_request">Save</button>
                        <button type="button" class="btn btn-danger btn-sm close-form-div_credit" data-dismiss="modal">
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="modal_status" class="modal fade">
        <div class="modal-dialog" style="">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-form-new" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h3 class="modal-title text-center title-status">Change Status</h3>
                </div>
                <div class="modal-body">
                    <div class="row content-body-status" id="data-status">
                        <form id="form-horizontal form-label-left" class="form-data-status" method="post" action="#">
                            <h5 class="message text-danger hidden text-center"></h5>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <input type="hidden" name="request_id" id="request_id" value="">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 list_status">
                                    <label class="control-label col-lg-2 col-md-2 col-sm-4 col-xs-12"
                                           for="status">Status:
                                    </label>
                                    <div class="col-lg-8 col-md-8 col-sm-10 col-xs-12">
                                        <select name="status" id="status" class="status_list form-control">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label col-lg-2 col-md-2 col-sm-4 col-xs-12"
                                           for="remark">Remark:
                                    </label>
                                    <div class="col-lg-8 col-md-8 col-sm-10 col-xs-12">
                                    <textarea name="remark" id="remark" cols="20" rows="2"
                                              class="form-control"></textarea>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label for="" class="col-lg-2 col-md-2 col-sm-4 col-xs-12"></label>
                                <button class="btn btn-danger btn-sm btn-cancel" type="reset">Cancel</button>
                                <button type="submit" class="btn btn-success btn-sm btn_save_remark">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger close-form-status" data-dismiss="modal">Close
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(asset('vendors/select2/dist/js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('vendors/moment/min/moment.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrapValidator.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap-datetimepicker.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/buttons.colVis.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.buttons.min.js')); ?>"></script>
    <script src="<?php echo e(asset('vendors/dataTables.fixedColumns/dataTables.fixedColumns.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.colReorder.min.js')); ?>"></script>
    <?php echo $__env->make('pages.backend.js.request.index', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('includes.master_backend', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>