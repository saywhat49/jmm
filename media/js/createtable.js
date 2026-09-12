/**
 * JMM - concepteur de tables.
 *
 * Deux responsabilites :
 *   1. Renumeroter les champs de chaque ligne apres ajout ou suppression.
 *      Les cases a cocher non cochees ne sont pas envoyees par le
 *      navigateur ; sans index explicite et continu, field_null[] et
 *      field_extra[] se decalaient par rapport a field_name[] et les
 *      attributs atterrissaient sur la mauvaise colonne.
 *   2. Adapter le champ Longueur au type choisi.
 */
(() => {
    'use strict';

    const TYPES = (window.Joomla && Joomla.getOptions)
        ? Joomla.getOptions('com_jmm.columnTypes', {})
        : {};

    const LABELS = (window.Joomla && Joomla.getOptions)
        ? Joomla.getOptions('com_jmm.createtable', {})
        : {};

    const FIELDS = ['field_name', 'field_type', 'field_length', 'field_null', 'field_key', 'field_extra', 'field_comments'];

    const tbody = document.getElementById('columns_tbody');
    const btnAdd = document.getElementById('btn_add_column');

    if (!tbody) {
        return;
    }

    /** Reecrit name="xxx[n]" sur toutes les lignes, dans l'ordre affiche. */
    const renumber = () => {
        Array.from(tbody.rows).forEach((row, index) => {
            FIELDS.forEach((field) => {
                const el = row.querySelector(`[name^="${field}["]`);
                if (el) {
                    el.name = `${field}[${index}]`;
                }
            });
        });
    };

    /** Active, desactive et annote le champ Longueur selon le type. */
    const syncLength = (row) => {
        const select = row.querySelector('.jmm-type-select');
        const length = row.querySelector('.jmm-length-input');

        if (!select || !length) {
            return;
        }

        const spec = TYPES[select.value] || 'int';

        if (spec === 'none') {
            length.value = '';
            length.disabled = true;
            length.placeholder = LABELS.lengthNone || '—';
            length.title = LABELS.lengthNone || '';
            return;
        }

        length.disabled = false;

        if (spec === 'decimal') {
            length.placeholder = LABELS.lengthDecimal || '10,2';
            length.title = LABELS.lengthDecimal || '';
        } else if (spec === 'fsp') {
            length.placeholder = LABELS.lengthFsp || '0-6';
            length.title = LABELS.lengthFsp || '';
        } else {
            length.placeholder = LABELS.lengthInt || '';
            length.title = spec === 'int_req' ? (LABELS.lengthInt || '') : '';
        }
    };

    const buildTypeOptions = (selected) => Object.keys(TYPES)
        .map((t) => `<option value="${t}"${t === selected ? ' selected' : ''}>${t}</option>`)
        .join('');

    if (btnAdd) {
        btnAdd.addEventListener('click', () => {
            const index = tbody.rows.length;
            const tr = document.createElement('tr');
            tr.className = 'jmm-col-row';
            tr.innerHTML = `
                <td><input type="text" name="field_name[${index}]" class="form-control" placeholder="column_${index + 1}" required></td>
                <td><select name="field_type[${index}]" class="form-select jmm-type-select">${buildTypeOptions('VARCHAR')}</select></td>
                <td><input type="text" name="field_length[${index}]" class="form-control jmm-length-input" value="255"></td>
                <td class="text-center"><input type="checkbox" name="field_null[${index}]" class="form-check-input" value="1"></td>
                <td>
                    <select name="field_key[${index}]" class="form-select">
                        <option value="none" selected>---</option>
                        <option value="primary">PRIMARY</option>
                        <option value="unique">UNIQUE</option>
                        <option value="index">INDEX</option>
                    </select>
                </td>
                <td class="text-center"><input type="checkbox" name="field_extra[${index}]" class="form-check-input" value="AUTO_INCREMENT"></td>
                <td><input type="text" name="field_comments[${index}]" class="form-control"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><span class="icon-trash" aria-hidden="true"></span></button></td>
            `;
            tbody.appendChild(tr);
            syncLength(tr);
            renumber();
        });
    }

    tbody.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-remove-row');

        if (!btn || btn.disabled) {
            return;
        }

        const tr = btn.closest('tr');

        if (tr && tbody.rows.length > 1) {
            tr.remove();
            renumber();
        }
    });

    tbody.addEventListener('change', (e) => {
        if (e.target.classList.contains('jmm-type-select')) {
            syncLength(e.target.closest('tr'));
        }
    });

    // Etat initial des lignes deja presentes.
    Array.from(tbody.rows).forEach(syncLength);
    renumber();
})();
