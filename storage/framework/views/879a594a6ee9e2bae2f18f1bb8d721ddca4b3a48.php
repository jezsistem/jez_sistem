<head>
	<meta charset="utf-8" />
	<title>JEZ SYSTEM | Point Of Sale</title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
	<meta name="description" content="Updates and statistics" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<!--begin::Fonts-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
	<!--end::Fonts-->
	<!--begin::Global Theme Styles(used by all pages)-->
	<link href="<?php echo e(asset('pos/css')); ?>/style.css?v=2.0" rel="stylesheet" type="text/css" />
	<!--end::Global Theme Styles-->
	<link href="<?php echo e(asset('pos/css')); ?>/pace-theme-flat-top.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo e(asset('pos/css')); ?>/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo e(asset('pos/css')); ?>/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo e(asset('pos/css')); ?>/multiple-select.min.css" rel="stylesheet">
    <link href="<?php echo e(asset('cdn/jquery.toast.min.css')); ?>" rel="stylesheet" type="text/css" >
    <link href="<?php echo e(asset('cdn/select2.min.css')); ?>" rel="stylesheet" type="text/css" >
	<link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('pos/css')); ?>/daterangepicker.css" />

	<style>
		#current-shift-table-wrapper {
			text-align: center;
		}

		#current-shift-table {
			margin: 0 auto;
			width: 50%;
			border-collapse: collapse;
		}

		#current-shift-table td, #current-shift-table th {
			padding: 10px;
			text-align: center;
			border: 1px solid #ddd;
		}

		#current-shift-table th {
			background-color: #f2f2f2;
		}
	</style>

	<script>
		var elem = document.documentElement;
		function forceFullScreen()
		{
			if (elem.requestFullscreen) {
				elem.requestFullscreen();
			} else if (elem.webkitRequestFullscreen) { /* Safari */
				elem.webkitRequestFullscreen();
			} else if (elem.msRequestFullscreen) { /* IE11 */
				elem.msRequestFullscreen();
			}
		}
	</script>
</head><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/offline_pos/offline_pos_head.blade.php ENDPATH**/ ?>