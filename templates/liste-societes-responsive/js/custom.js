/**
 * Modèle JMM « sociétés adhérentes ».
 * Recherche instantanée + tri par colonne. Aucune dépendance.
 *
 * Le script est chargé en "defer" : il s'auto-initialise sur chaque
 * conteneur [data-jmm-table] présent dans la page.
 */
(function () {
    'use strict';

    function normalize(value) {
        return (value || '')
            .toString()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // accents
            .trim();
    }

    /** Convertit en nombre si la cellule en contient un (gère 1 234,56 et 1,234.56). */
    function asNumber(text) {
        var cleaned = text.replace(/[\s\u00a0]/g, '');

        if (/^-?\d{1,3}(\.\d{3})+(,\d+)?$/.test(cleaned) || /^-?\d+,\d+$/.test(cleaned)) {
            cleaned = cleaned.replace(/\./g, '').replace(',', '.');
        } else {
            cleaned = cleaned.replace(/,/g, '');
        }

        cleaned = cleaned.replace(/[^\d.\-]/g, '');

        if (cleaned === '' || cleaned === '-' || isNaN(cleaned)) {
            return null;
        }

        return parseFloat(cleaned);
    }

    function initTable(root) {
        var table = root.querySelector('.jmm-table');
        var tbody = table ? table.querySelector('tbody') : null;

        if (!tbody) {
            return;
        }

        var rows = Array.prototype.slice.call(tbody.rows);
        var search = root.querySelector('[data-jmm-search]');
        var counter = root.querySelector('[data-jmm-count]');
        var empty = root.querySelector('[data-jmm-empty]');
        var headers = Array.prototype.slice.call(table.querySelectorAll('thead th[data-jmm-sort]'));

        // Texte de recherche mis en cache une fois pour toutes.
        rows.forEach(function (row) {
            row.dataset.jmmHaystack = normalize(row.textContent);
        });

        function restripe() {
            var visible = 0;

            rows.forEach(function (row) {
                if (row.hidden) {
                    return;
                }

                row.classList.remove('row0', 'row1');
                row.classList.add(visible % 2 ? 'row1' : 'row0');
                visible++;
            });

            if (counter) {
                counter.textContent = visible;
            }

            if (empty) {
                empty.hidden = visible !== 0;
            }
        }

        function filter() {
            var needle = normalize(search ? search.value : '');

            rows.forEach(function (row) {
                row.hidden = needle !== '' && row.dataset.jmmHaystack.indexOf(needle) === -1;
            });

            restripe();
        }

        if (search) {
            var timer = null;

            search.addEventListener('input', function () {
                window.clearTimeout(timer);
                timer = window.setTimeout(filter, 120);
            });
        }

        function sortBy(index, ascending) {
            var sorted = rows.slice().sort(function (a, b) {
                var ta = (a.cells[index] ? a.cells[index].textContent : '').trim();
                var tb = (b.cells[index] ? b.cells[index].textContent : '').trim();
                var na = asNumber(ta);
                var nb = asNumber(tb);
                var result;

                if (na !== null && nb !== null) {
                    result = na - nb;
                } else {
                    result = normalize(ta).localeCompare(normalize(tb), 'fr', { numeric: true });
                }

                return ascending ? result : -result;
            });

            var fragment = document.createDocumentFragment();

            sorted.forEach(function (row) {
                fragment.appendChild(row);
            });

            tbody.appendChild(fragment);
            rows = sorted;
            restripe();
        }

        headers.forEach(function (header, index) {
            function toggle() {
                var ascending = header.getAttribute('aria-sort') !== 'ascending';

                headers.forEach(function (other) {
                    other.setAttribute('aria-sort', 'none');
                });

                header.setAttribute('aria-sort', ascending ? 'ascending' : 'descending');
                sortBy(index, ascending);
            }

            header.addEventListener('click', toggle);

            header.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    toggle();
                }
            });
        });

        restripe();
    }

    function boot() {
        document.querySelectorAll('[data-jmm-table]').forEach(initTable);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
}());
