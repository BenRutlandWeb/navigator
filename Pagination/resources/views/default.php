<?php if ($paginator->hasPages()) : ?>
    <nav>
        <ul class="pagination">
            <?php if ($paginator->onFirstPage()) : ?>
                <li class="disabled" aria-disabled="true" aria-label="<?php echo esc_attr_x('Previous', 'Pagination previous'); ?>">
                    <span aria-hidden="true">&lsaquo;</span>
                </li>
            <?php else : ?>
                <li>
                    <a href="<?php echo esc_url($paginator->previousPageUrl()); ?>" rel="prev" aria-label="<?php echo esc_attr_x('Previous', 'Pagination previous'); ?>">&lsaquo;</a>
                </li>
            <?php endif; ?>

            <?php foreach ($elements as $page => $url) : ?>
                <?php if ($page == $paginator->currentPage()) : ?>
                    <li class="active" aria-current="page"><span><?php echo esc_html($page); ?></span></li>
                <?php else : ?>
                    <li><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($page); ?></a></li>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if ($paginator->hasMorePages()) : ?>
                <li>
                    <a href="<?php echo esc_url($paginator->nextPageUrl()); ?>" rel="next" aria-label="<?php echo esc_attr_x('Next', 'Pagination next'); ?>">&rsaquo;</a>
                </li>
            <?php else : ?>
                <li class="disabled" aria-disabled="true" aria-label="<?php echo esc_attr_x('Next', 'Pagination next'); ?>">
                    <span aria-hidden="true">&rsaquo;</span>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>