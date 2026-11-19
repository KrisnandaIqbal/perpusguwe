<?php $pager->setSurroundCount(2) ?>

<nav aria-label="Navigasi Halaman Katalog">
    <ul class="pagination opac-custom-pagination align-items-center justify-content-center justify-content-md-end mb-0 gap-1">
        <?php if ($pager->hasPrevious()): ?>
            <li class="page-item">
                <a class="page-link opac-page-control" href="<?= $pager->getPrevious() ?>" aria-label="Halaman Sebelumnya">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link opac-page-control">
                    <i class="fas fa-chevron-left"></i>
                </span>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link opac-page-num" href="<?= $link['uri'] ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()): ?>
            <li class="page-item">
                <a class="page-link opac-page-control" href="<?= $pager->getNext() ?>" aria-label="Halaman Berikutnya">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link opac-page-control">
                    <i class="fas fa-chevron-right"></i>
                </span>
            </li>
        <?php endif ?>
    </ul>
</nav>