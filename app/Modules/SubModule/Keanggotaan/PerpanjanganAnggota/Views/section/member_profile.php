<?php
$request = service('request');
$defaultAvatar = base_url('uploads/default/avatar_neutral.svg');
?>

<div class="card-shadow-dark profile-responsive card-border mb-3 card">
	<div class="dropdown-menu-header">
		<div class="dropdown-menu-header-inner bg-corporate-primary2">
			<div class="menu-header-image" style="background-image: url('<?= base_url('themes/uigniter') ?>/images/dropdown-header/abstract8.jpg')"></div>
			<div class="menu-header-content btn-pane-right">
				<div class="avatar-icon-wrapper mr-2 avatar-icon-xl">
					<div class="avatar-icon">
						<img id="profile_avatar" src="<?= $defaultAvatar ?>" onerror="this.onerror=null;this.src='<?= $defaultAvatar ?>';" alt="Foto Anggota" style="object-fit: cover;">
					</div>
				</div>
				<div>
					<h5 class="menu-header-title mb-1"><span id="name">Pilih Anggota</span></h5>
					<h6 class="menu-header-subtitle"><span name="no_anggota">-</span></h6>
				</div>
				<div class="menu-header-btn-pane"></div>
			</div>
		</div>
	</div>
	<ul class="list-group list-group-flush" style="font-size: 13px;">
		<li class="list-group-item py-2 px-3">
			<div class="widget-content p-0">
				<div class="widget-content-wrapper">
					<div class="widget-content-left mr-3">
						<i class="fa fa-id-badge text-primary" style="font-size: 16px;"></i>
					</div>
					<div class="widget-content-left">
						<div class="text-muted small" style="font-size: 11px;">Kategori Keanggotaan</div>
						<div class="widget-heading" id="profile_kategori_badge"><span class="badge badge-secondary">-</span></div>
					</div>
				</div>
			</div>
		</li>
		<li class="list-group-item py-2 px-3">
			<div class="widget-content p-0">
				<div class="widget-content-wrapper">
					<div class="widget-content-left mr-3">
						<i class="fa fa-building text-info" style="font-size: 16px;"></i>
					</div>
					<div class="widget-content-left">
						<div class="text-muted small" style="font-size: 11px;">Unit Kerja (Eselon I & II)</div>
						<div class="widget-heading font-weight-bold" id="profile_unit_kerja" style="font-size: 12.5px;">-</div>
					</div>
				</div>
			</div>
		</li>
		<li class="list-group-item py-2 px-3">
			<div class="widget-content p-0">
				<div class="widget-content-wrapper">
					<div class="widget-content-left mr-3">
						<i class="fa fa-clock text-warning" style="font-size: 16px;"></i>
					</div>
					<div class="widget-content-left">
						<div class="text-muted small" style="font-size: 11px;">Masa Berlaku Saat Ini</div>
						<div class="widget-heading font-weight-bold" id="date">-</div>
					</div>
				</div>
			</div>
		</li>
		<li class="list-group-item py-2 px-3">
			<div class="widget-content p-0">
				<div class="widget-content-wrapper">
					<div class="widget-content-left mr-3">
						<i class="fa fa-phone text-success" style="font-size: 16px;"></i>
					</div>
					<div class="widget-content-left">
						<div class="text-muted small" style="font-size: 11px;">No. HP / WhatsApp</div>
						<div class="widget-heading"><span id="nomor">-</span></div>
					</div>
				</div>
			</div>
		</li>
		<li class="list-group-item py-2 px-3">
			<div class="widget-content p-0">
				<div class="widget-content-wrapper">
					<div class="widget-content-left mr-3">
						<i class="fa fa-envelope text-secondary" style="font-size: 16px;"></i>
					</div>
					<div class="widget-content-left">
						<div class="text-muted small" style="font-size: 11px;">Email</div>
						<div class="widget-heading"><span name="email">-</span></div>
					</div>
				</div>
			</div>
		</li>
		<li class="list-group-item py-2 px-3">
			<div class="widget-content p-0">
				<div class="widget-content-wrapper">
					<div class="widget-content-left mr-3">
						<i class="fa fa-map-marker text-danger" style="font-size: 16px;"></i>
					</div>
					<div class="widget-content-left">
						<div class="text-muted small" style="font-size: 11px;">Alamat</div>
						<div class="widget-heading"><span name="address">-</span></div>
					</div>
				</div>
			</div>
		</li>
	</ul>
</div>
