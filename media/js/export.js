/**
 * JMM CSV Export Handler (Vanilla ES6)
 */
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.jmm-export-btn');
        if (!btn) return;

        const query = btn.dataset.query || '';
        const filename = btn.dataset.filename || 'export';
        const dbname = btn.dataset.dbname || '';

        if (!query) {
            alert('No query specified for export.');
            return;
        }

        let csrfToken = Joomla.getOptions('csrf.token', '');
        if (!csrfToken) {
            const hiddenToken = document.querySelector('#adminForm input[type="hidden"][value="1"]');
            if (hiddenToken) {
                csrfToken = hiddenToken.name;
            }
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?option=com_jmm&task=export.csv';
        form.target = '_blank';

        const queryInput = document.createElement('input');
        queryInput.type = 'hidden';
        queryInput.name = 'query';
        queryInput.value = query;
        form.appendChild(queryInput);

        const filenameInput = document.createElement('input');
        filenameInput.type = 'hidden';
        filenameInput.name = 'filename';
        filenameInput.value = filename;
        form.appendChild(filenameInput);

        if (dbname) {
            const dbInput = document.createElement('input');
            dbInput.type = 'hidden';
            dbInput.name = 'dbname';
            dbInput.value = dbname;
            form.appendChild(dbInput);
        }

        if (csrfToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = csrfToken;
            tokenInput.value = '1';
            form.appendChild(tokenInput);
        }

        document.body.appendChild(form);
        form.submit();
        form.remove();
    });
});