<?php
/**
 * @package   JMM
 * @license   GNU/GPL
 *
 * Modèle « sociétés Tourisme et Patrimoine » — affichage responsive.
 *
 * Variables disponibles :
 *   $rows   tableau de lignes associatives
 *   $cols   noms des colonnes
 *   $params paramètres de l'élément de menu
 *   $this   la vue (pagination, escape, ...)
 */
defined('_JEXEC') or die('Restricted access');

// Échappement (déclaré une seule fois, le modèle pouvant être inclus plusieurs fois).
if (!function_exists('jmmEsc')) {
    function jmmEsc($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

// Libellé lisible pour un nom de colonne : "raison_sociale" -> "Raison sociale".
if (!function_exists('jmmColLabel')) {
    function jmmColLabel(string $col): string
    {
        return ucfirst(str_replace('_', ' ', $col));
    }
}

$total = count($rows);

// Colonnes dont le contenu doit rester du HTML (liens, images...).
// Ajoutez-y vos noms de colonnes si besoin.
$rawCols = [];
?>
<div class="jmm-societes" data-jmm-table>

    <div class="jmm-toolbar">
        <h3 class="jmm-title">
            Liste des <span data-jmm-count><?php echo $total; ?></span> sociétés Tourisme et Patrimoine
        </h3>

        <div class="jmm-search">
            <label class="jmm-visually-hidden" for="jmm-search-input">Rechercher</label>
            <input
                type="search"
                id="jmm-search-input"
                class="jmm-search-input"
                data-jmm-search
                placeholder="Rechercher une société…"
                autocomplete="off">
        </div>
    </div>

    <div class="jmm-table-wrapper">
        <table class="jmm-table">
            <thead>
                <tr>
                    <?php foreach ($cols as $col): ?>
                        <th scope="col" data-jmm-sort tabindex="0" role="button" aria-sort="none">
                            <span class="jmm-th-label"><?php echo jmmEsc(jmmColLabel($col)); ?></span>
                            <span class="jmm-sort-icon" aria-hidden="true"></span>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $i => $row): ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <?php foreach ($cols as $col): ?>
                            <?php $val = $row[$col] ?? ''; ?>
                            <td data-label="<?php echo jmmEsc(jmmColLabel($col)); ?>">
                                <?php echo in_array($col, $rawCols, true) ? $val : jmmEsc($val); ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <p class="jmm-empty" data-jmm-empty hidden>Aucune société ne correspond à votre recherche.</p>
</div>
