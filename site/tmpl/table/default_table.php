<?php
defined('_JEXEC') or die;
?>
<div class="table-responsive shadow-sm rounded border bg-white">
    <table class="table table-striped table-hover table-bordered align-middle mb-0">
        <thead class="table-dark">
            <tr>
                <?php foreach ($this->columns as $col): ?>
                    <th scope="col" class="py-3"><?php echo $this->escape(ucwords(str_replace('_', ' ', $col))); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->items as $row): ?>
                <tr>
                    <?php foreach ($row as $colName => $val): ?>
                        <td>
                            <?php
                            $strVal = (string) $val;
                            $lower = strtolower($strVal);
                            // Les valeurs numeriques 0 et 1 ne sont plus transformees en badge :
                            // une colonne d'identifiants ou de quantites restait illisible.
                            if ($lower === 'oui' || $lower === 'yes'): ?>
                                <span class="badge bg-success"><?php echo $this->escape($strVal); ?></span>
                            <?php elseif ($lower === 'non' || $lower === 'no'): ?>
                                <span class="badge bg-secondary"><?php echo $this->escape($strVal); ?></span>
                            <?php elseif (filter_var($strVal, FILTER_VALIDATE_EMAIL)): ?>
                                <a href="mailto:<?php echo $this->escape($strVal); ?>" class="text-decoration-none"><?php echo $this->escape($strVal); ?></a>
                            <?php else: ?>
                                <?php echo htmlspecialchars($strVal, ENT_QUOTES, 'UTF-8'); ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>