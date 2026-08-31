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
                            if ($lower === 'oui' || $lower === 'yes' || $lower === '1' && strlen($strVal) === 1): ?>
                                <span class="badge bg-success"><?php echo $this->escape($strVal); ?></span>
                            <?php elseif ($lower === 'non' || $lower === 'no' || $lower === '0' && strlen($strVal) === 1): ?>
                                <span class="badge bg-secondary"><?php echo $this->escape($strVal); ?></span>
                            <?php elseif (filter_var($strVal, FILTER_VALIDATE_EMAIL)): ?>
                                <a href="mailto:<?php echo $this->escape($strVal); ?>" class="text-decoration-none"><?php echo $this->escape($strVal); ?></a>
                            <?php elseif (preg_match('/^(0[1-9])(?:[ .-]?[0-9]{2}){4}$/', $strVal)): ?>
                                <a href="tel:<?php echo $this->escape(preg_replace('/[^0-9+]/', '', $strVal)); ?>" class="text-decoration-none font-monospace">
                                    <span class="icon-phone me-1" aria-hidden="true"></span><?php echo $this->escape($strVal); ?>
                                </a>
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