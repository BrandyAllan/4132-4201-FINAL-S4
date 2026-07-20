<?php $pager->setSurroundCount(2); ?>

<nav aria-label="Pagination">
    <ul class="pagination justify-content-center mb-0">

        <?php if ($pager->hasPrevious()): ?>
            <li class="page-item">
                <a
                    class="page-link"
                    href="<?= $pager->getFirst() ?>"
                >
                    Première
                </a>
            </li>

            <li class="page-item">
                <a
                    class="page-link"
                    href="<?= $pager->getPrevious() ?>"
                >
                    Précédente
                </a>
            </li>
        <?php endif; ?>

        <?php foreach (
            $pager->links() as $link
        ): ?>
            <li
                class="page-item
                <?= $link['active']
                    ? 'active'
                    : '' ?>"
            >
                <a
                    class="page-link"
                    href="<?= $link['uri'] ?>"
                >
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach; ?>

        <?php if ($pager->hasNext()): ?>
            <li class="page-item">
                <a
                    class="page-link"
                    href="<?= $pager->getNext() ?>"
                >
                    Suivante
                </a>
            </li>

            <li class="page-item">
                <a
                    class="page-link"
                    href="<?= $pager->getLast() ?>"
                >
                    Dernière
                </a>
            </li>
        <?php endif; ?>

    </ul>
</nav>