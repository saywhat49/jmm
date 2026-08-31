<?php
defined('_JEXEC') or die;
?>
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php foreach ($this->items as $row): ?>
        <div class="col">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-primary">
                <div class="card-body">
                    <?php
                    $titleCol = $this->columns[1] ?? $this->columns[0];
                    $mainTitle = $row[$titleCol] ?? ($row['SOCIETE'] ?? ($row['title'] ?? ''));
                    ?>
                    <h5 class="card-title text-primary fw-bold mb-3">
                        <span class="icon-building me-1" aria-hidden="true"></span>
                        <?php echo $this->escape($mainTitle); ?>
                    </h5>

                    <ul class="list-group list-group-flush mb-0">
                        <?php foreach ($row as $colName => $val): ?>
                            <?php
                            if ($colName === $titleCol) continue;
                            $strVal = (string) $val;
                            if ($strVal === '') continue;
                            ?>
                            <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <span class="text-muted small fw-semibold"><?php echo $this->escape(ucwords(str_replace('_', ' ', $colName))); ?>:</span>
                                <div>
                                    <?php if (preg_match('/^(0[1-9])(?:[ .-]?[0-9]{2}){4}$/', $strVal)): ?>
                                        <a href="tel:<?php echo $this->escape(preg_replace('/[^0-9+]/', '', $strVal)); ?>" class="btn btn-sm btn-outline-primary py-0">
                                            <span class="icon-phone me-1" aria-hidden="true"></span><?php echo $this->escape($strVal); ?>
                                        </a>
                                    <?php elseif (strtolower($strVal) === 'oui'): ?>
                                        <span class="badge bg-success">Oui</span>
                                    <?php elseif (strtolower($strVal) === 'non'): ?>
                                        <span class="badge bg-secondary">Non</span>
                                    <?php else: ?>
                                        <span class="fw-medium text-end"><?php echo htmlspecialchars($strVal, ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>