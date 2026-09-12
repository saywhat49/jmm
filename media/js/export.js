/**
 * JMM - export CSV (ES6, sans dependance).
 *
 * Le bouton .jmm-export-btn poste une requete SQL vers
 * index.php?option=com_jmm&task=export.csv dans un nouvel onglet.
 */
(() => {
    'use strict';

    /**
     * Jeton CSRF. Joomla le publie sous forme d'option de script, mais
     * seulement si la page a appele HTMLHelper::_('behavior.core') ;
     * charger l'asset "core" ne suffit pas toujours. On retombe alors sur
     * le champ cache que JHtml form.token depose dans le formulaire de la
     * page : son NOM est le jeton, sa valeur vaut 1.
     */
    const getCsrfToken = () => {
        if (window.Joomla && typeof Joomla.getOptions === 'function') {
            const token = Joomla.getOptions('csrf.token', '');
            if (token) {
                return token;
            }
        }

        const field = document.querySelector('input[type="hidden"][value="1"][name]');

        return field && /^[0-9a-f]{32}$/i.test(field.name) ? field.name : '';
    };

    const submitExport = (btn) => {
        const query = btn.dataset.query || '';
        const filename = btn.dataset.filename || 'export';
        const dbname = btn.dataset.dbname || '';

        if (!query) {
            window.alert('Aucune requete associee a ce bouton.');
            return;
        }

        const token = getCsrfToken();

        if (!token) {
            window.alert('Jeton de securite introuvable. Rechargez la page puis reessayez.');
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?option=com_jmm&task=export.csv';
        form.target = '_blank';
        form.style.display = 'none';

        const fields = { query, filename, dbname };
        fields[token] = '1';

        Object.entries(fields).forEach(([name, value]) => {
            if (value === '') {
                return;
            }

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();

        // Le retrait immediat peut annuler l'envoi sur certains navigateurs.
        window.setTimeout(() => form.remove(), 1000);
    };

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.jmm-export-btn');

        if (!btn) {
            return;
        }

        e.preventDefault();

        try {
            submitExport(btn);
        } catch (err) {
            console.error('JMM export:', err);
            window.alert('Export impossible : ' + err.message);
        }
    });
})();
