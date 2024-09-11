<!-- pos header -->
<header class="pos-header bg-primary">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-xl-4 col-lg-4 col-md-6">
                <div class="greeting-text">
                    <a href="<?php echo e(url('/redirect')); ?>">

                        <img style="width:100px; margin-left: 25px;" alt="Logo" src="<?php echo e(asset('logo')); ?>/jez_pro_putih.png" />
                    </a>



                </div>
            </div>
            <div class="col-xl-4 col-lg-5 col-md-6 clock-main">
                <div class="clock">
                    <div class="datetime-content">
                        <ul>
                            <li id="hours"></li>
                            <li id="point1">:</li>
                            <li id="min"></li>
                            <li id="point">:</li>
                            <li id="sec"></li>
                        </ul>
                    </div>
                    <div class="datetime-content">
                        <div id="Date" class=""></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-3 col-md-12  order-lg-last order-second">
                <div class="topbar justify-content-end">
                    
                    <div class="col-md-6">
                        Hallo, <?php echo e($data['user']->u_name); ?>

                    </div>












                    <div class="topbar-item">

                        <?php if($data['shift_status'] == 0): ?>
                        <div class="btn btn-icon w-auto h-auto btn-clean d-flex align-items-center py-0 mr-3" id="startShiftButton" style="display: none;" >
                            <!-- <span class="badge badge-pill badge-primary">5</span> -->
                            <span class="symbol symbol-35 symbol-light-success">
                                <span class="symbol-label bg-warning font-size-h5">
                                    <i class="fas fa-power-off" style="color: white;"></i>
                                </span>
                            </span>
                        </div>
                        <?php else: ?>
                        <div class="btn btn-icon w-auto h-auto btn-clean d-flex align-items-center py-0 mr-3" id="stopShiftButton" style="display: none;">
                            <span class="symbol symbol-35 symbol-light-danger">
                                <span class="symbol-label bg-danger font-size-h5">
                                    <i class="fas fa-power-off" style="color: white;"></i>
                                </span>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="dropdown mega-dropdown">
                        <div id="id2" class="topbar-item " data-toggle="dropdown" data-display="static">
                            <div class="btn btn-icon w-auto h-auto btn-clean d-flex align-items-center py-0 mr-3">
                                <span class="symbol symbol-35 symbol-light-success">
                                    <span class="symbol-label bg-primary  font-size-h5 ">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="#fff"
                                             class="bi bi-calculator-fill" viewBox="0 0 16 16">
                                            <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm2 .5v2a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 0-.5-.5h-7a.5.5 0 0 0-.5.5zm0 4v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5zM4.5 9a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zM4 12.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5zM7.5 6a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zM7 9.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5zm.5 2.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zM10 6.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5zm.5 2.5a.5.5 0 0 0-.5.5v4a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-4a.5.5 0 0 0-.5-.5h-1z"/>
                                        </svg>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="dropdown-menu dropdown-menu-right calu" style="min-width: 248px;">
                            <div class="calculator">
                                <div class="input" ><p id="input"></p></div>
                                <div class="buttons">
                                    <div class="operators">
                                        <div>+</div>
                                        <div>-</div>
                                        <div>&times;</div>
                                        <div>&divide;</div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div class="leftPanel">
                                            <div class="numbers">
                                                <div>7</div>
                                                <div>8</div>
                                                <div>9</div>
                                            </div>
                                            <div class="numbers">
                                                <div>4</div>
                                                <div>5</div>
                                                <div>6</div>
                                            </div>
                                            <div class="numbers">
                                                <div>1</div>
                                                <div>2</div>
                                                <div>3</div>
                                            </div>
                                            <div class="numbers">
                                                <div>0</div>
                                                <div>.</div>
                                                <div id="clear">C</div>
                                            </div>
                                        </div>
                                        <div class="equal" id="result">=</div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <a href="#" class="btn btn-primary white mb-2" id="add_custom_amount">
                                        Add Custom Amount
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="topbar-item folder-data">
                        <div class="btn btn-icon  w-auto h-auto btn-clean d-flex align-items-center py-0 mr-3"
                             data-toggle="modal" data-target="#shiftDetailModal" id="shiftEmployeeBtn">
                            <!-- <span class="badge badge-pill badge-primary">5</span> -->
                            <span class="symbol symbol-35  symbol-light-success">
                                <span class="symbol-label bg-warning font-size-h5 ">
                                    <svg width="20px" height="20px" xmlns="http://www.w3.org/2000/svg" fill="#ffff"
                                         viewBox="0 0 16 16">
                                        <path d="M9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31L.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3zm-8.322.12C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139z"></path>
                                    </svg>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/offline_pos/offline_pos_header.blade.php ENDPATH**/ ?>