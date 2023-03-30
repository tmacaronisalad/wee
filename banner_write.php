<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="icon" href="<?= base_url('assets/dashboard/img/logo.png') ?>" />
	<link rel="apple-touch-icon" href="<?= base_url('assets/dashboard/img/logo.png') ?>" />
	<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/admin.min.css') ?>" />
	<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/perfect-scrollbar.min.css') ?>" />
	<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/sweetalert.min.css') ?>" />
	<title>Banner Write</title>
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body class="skin-blue fixed-layout">
	<div id="main-wrapper">
<?php
	$this->load->view('dashboard/_header');
?>
		<div class="page-wrapper">
			<div class="container-fluid">
				<div class="row page-titles">
					<div class="col-md-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="<?= base_url('dashboard/banner/page/1') ?>">Banner</a></li>
							<li class="breadcrumb-item active">Write</li>
						</ol>
					</div>
				</div>
				<div class="row">
					<div class="col-md-8 text-left"></div>
					<div class="col-md-4 text-right m-b-20">
						<a href="<?= base_url('dashboard/banner/page/1') ?>" class="btn btn-danger"><i class="fa fa-chevron-left"></i> &nbsp; Back</a>
					</div>
				</div>
				<form action="<?= base_url('dashboard/banner/add') ?>" method="post">
					<input type="hidden" name="form-key" value="<?= $param_key ?>" />
					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-body">
									<h4 class="card-title p-b-10 border-bottom">Banner</h4>
									<div class="form-body">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="text-danger">Title</label>
													<input type="text" class="form-control" name="form-title-jp" maxlength="64" required />
												</div>
											</div>
<!-- 											<div class="col-md-6">
												<div class="form-group">
													<label class="text-danger">Title (EN)</label>
													<input type="text" class="form-control" name="form-title-en" maxlength="64" required />
												</div>
											</div> -->
<!-- 											<div class="col-md-12">
												<div class="form-group">
													<label class="text-danger">Content (JP)</label>
													<textarea class="form-control wysiwyg" name="form-content-jp" rows="5" required></textarea>
												</div>
											</div> -->
<!-- 											<div class="col-md-12">
												<div class="form-group">
													<label class="text-danger">Content (EN)</label>
													<textarea class="form-control wysiwyg" name="form-content-en" rows="5" required></textarea>
												</div>
											</div> -->
											<div class="col-md-12">
												<div class="form-group">
													<label class="text-danger">Image <span class="text-info font-10">(Recommended Dimensions 640px x 420px)</span></label>
													<div class="uploader-wrapper" id="uploader-attach">
														<input type="file" name="form-file[]" class="uploader-loader hide" data-action="<?= base_url('attach/add') ?>" data-key="<?= $param_key ?>" data-path="private" data-category="image" data-max="1" />
														<div class="uploader-area text-center">
															<div class="uploader-icon"><i class="fa fa-cloud-upload fa-4x"></i></div>
															<div class="uploader-message">Click here to upload.</div>
														</div>
														<div class="clearfix"></div>
														<div class="uploader-files"></div>
														<div class="clearfix"></div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 m-b-20">
							<div class="float-right">
								<button type="submit" class="btn btn-info btn-rounded btn-submit m-l-5 m-b-5"><i class="fa fa-magic"></i> &nbsp; S U B M I T</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
<?php
	$this->load->view('dashboard/_footer');
?>
	</div>
	<script src="<?= base_url('assets/dashboard/js/jquery.min.js') ?>"></script>
	<script src="<?= base_url('assets/dashboard/js/popper.min.js') ?>"></script>
	<script src="<?= base_url('assets/dashboard/js/bootstrap.min.js') ?>"></script>
	<script src="<?= base_url('assets/dashboard/js/perfect-scrollbar.min.js') ?>"></script>
	<script src="<?= base_url('assets/dashboard/js/sweetalert.min.js') ?>"></script>
	<script src="<?= base_url('assets/dashboard/js/admin.min.js') ?>"></script>
</body>
</html>
