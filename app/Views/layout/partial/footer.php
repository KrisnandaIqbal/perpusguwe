<?php
$nama_perpustakaan = $this->_getPerpustakaanName();
$year = date('Y');
?>
<div class="app-wrapper-footer">
    <div class="app-footer">
        <div class="app-footer__inner">
            <div class="app-footer-left">
                  &copy; <?= $year ?> <?= $nama_perpustakaan ?>. All rights reserved.
                </div>
            </div>
            <div class="app-footer-right">
                <div class="footer-copyright">
                
            </div>
        </div>      
    </div>
</div>