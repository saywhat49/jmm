/**
 * JMM SQL Execution & AJAX Operations (Vanilla ES6)
 */
document.addEventListener('DOMContentLoaded', () => {
    const btnSaveCanned = document.getElementById('btn_save_canned');
    const btnSaveSiteTable = document.getElementById('btn_save_sitetable');
    const queryEditor = document.getElementById('sql_query_editor');
    const dbSelect = document.getElementById('sql_dbname');

    function getCsrfToken() {
        const tokenInput = document.querySelector('input[name="task"] + input[type="hidden"]');
        if (tokenInput && tokenInput.value === '1') {
            return tokenInput.name;
        }
        return Joomla.getOptions('csrf.token', '');
    }

    if (btnSaveCanned) {
        btnSaveCanned.addEventListener('click', async () => {
            const query = queryEditor ? queryEditor.value.trim() : '';
            if (!query) {
                alert('Please enter a SQL query first.');
                return;
            }

            const title = prompt('Enter a title for this Canned Query:');
            if (!title || !title.trim()) {
                return;
            }

            const dbname = dbSelect ? dbSelect.value : '';
            const csrfToken = getCsrfToken();

            const formData = new FormData();
            formData.append('title', title.trim());
            formData.append('query', query);
            formData.append('dbname', dbname);
            if (csrfToken) {
                formData.append(csrfToken, '1');
            }

            try {
                const response = await fetch('index.php?option=com_jmm&task=sql.saveCannedQuery&format=json', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const result = await response.json();

                if (result.success) {
                    alert(result.message || 'Canned Query saved successfully!');
                    const select = document.getElementById('sql_canned_queries');
                    if (select && result.data) {
                        const opt = document.createElement('option');
                        opt.value = result.data.query;
                        opt.textContent = result.data.title;
                        select.appendChild(opt);
                    }
                } else {
                    alert('Error: ' + (result.message || 'Failed to save canned query.'));
                }
            } catch (err) {
                alert('Network or server error while saving query.');
            }
        });
    }

    if (btnSaveSiteTable) {
        btnSaveSiteTable.addEventListener('click', async () => {
            const query = queryEditor ? queryEditor.value.trim() : '';
            if (!query) {
                alert('Please enter a SQL query first.');
                return;
            }

            const title = prompt('Enter a title for this Site Table:');
            if (!title || !title.trim()) {
                return;
            }

            const dbname = dbSelect ? dbSelect.value : '';
            const csrfToken = getCsrfToken();

            const formData = new FormData();
            formData.append('title', title.trim());
            formData.append('query', query);
            formData.append('dbname', dbname);
            if (csrfToken) {
                formData.append(csrfToken, '1');
            }

            try {
                const response = await fetch('index.php?option=com_jmm&task=sql.saveSiteTable&format=json', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const result = await response.json();

                if (result.success) {
                    alert(result.message || 'Site Table saved successfully!');
                    const select = document.getElementById('sql_site_tables');
                    if (select && result.data) {
                        const opt = document.createElement('option');
                        opt.value = result.data.query;
                        opt.textContent = result.data.title;
                        select.appendChild(opt);
                    }
                } else {
                    alert('Error: ' + (result.message || 'Failed to save site table.'));
                }
            } catch (err) {
                alert('Network or server error while saving site table.');
            }
        });
    }
});