<?php $__env->startSection('style'); ?>
    <style type="text/css">
        /*.login-form {*/
        /*width: 340px;*/
        /*margin: 50px auto;*/
        /*}*/
        .login-form form {
            margin-bottom: 15px;
            background: #f7f7f7;
            box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
            padding: 30px;
        }

        .login-form h2 {
            margin: 0 0 15px;
        }

        /*.form-control, .btn {*/
        /*min-height: 38px;*/
        /*border-radius: 2px;*/
        /*}*/
        .input-group-addon .fa {
            font-size: 18px;
        }

        /*.btn {*/
        /*font-size: 15px;*/
        /*font-weight: bold;*/
        /*}*/
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <h2>
                    
                    <img src="data:image/jpeg;base64,<?php echo e(\Illuminate\Support\Facades\Session::get("logo")); ?>"
                         alt="TengLay Logo" class="img-circle" style="width: 10%;">
                    
                    <lable class="title-logo"><?php echo e(\Illuminate\Support\Facades\Session::get("name")); ?></lable>
                </h2>
            </div>
            
            
            
        </div>
        <div class="col-middle">
            <div class="text-center">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <h1>Ticket</h1>
                </div>
                <div class="clearfix">&nbsp;</div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="col-lg-4 col-md-4 col-sm-2 col-xs-12 hidden-xs">&nbsp;</div>
                    <div class="col-lg-4 col-md-4 col-sm-8 col-xs-12">
                        <div class="login-form">

                            <form action="<?php echo e(url('admin/do_login')); ?>" method="post" autocomplete="off">
                                <h2 class="text-center">Please Log in to your account</h2>
                                <br/>
                                <?php if($errors->any()): ?>
                                    <h5 class="login-box-msg text-danger"><?php echo e($errors->first()); ?></h5>
                                    <br>
                                <?php endif; ?>
                                <div class="form-group">
                                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <input type="text" class="form-control" placeholder="Enter Username"
                                               required="required" autofocus name="username">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <p class="hidden text-danger" id="msg"></p>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                        <input type="password" id="password" autofocus="" name="password"
                                               class="form-control" placeholder="Enter PIN Number" required="required">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-3">&nbsp;</div>
                                    <div class="col-lg-3 col-md-4 col-sm-3 col-xs-5">
                                        <button type="submit"
                                                class="btn btn-small btn-flat btn-primary btn-block col-xs-5">Log in
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <br>
                                    <br>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-2 col-xs-12 hidden-xs">&nbsp;</div>
                </div>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script type="text/javascript"
            src="<?php echo e(url('js/jquery.idle.js')); ?>"></script>
    <script type="text/javascript">
        $(document).idle({
            onIdle: function () {
                setTimeout(function () {
                    window.location = "<?php echo e(url('/admin/logout')); ?>";
                }, 1800000);
            },
        });

    </script>
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('includes.master_frontend', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>