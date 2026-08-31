/**
 * JMM Create Table Interactive Form (Vanilla ES6)
 */
document.addEventListener('DOMContentLoaded', () => {
    const btnAddColumn = document.getElementById('btn_add_column');
    const tbody = document.getElementById('columns_tbody');

    if (btnAddColumn && tbody) {
        btnAddColumn.addEventListener('click', () => {
            const rowCount = tbody.querySelectorAll('tr').length;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" name="field_name[]" class="form-control" placeholder="column_${rowCount + 1}" required></td>
                <td>
                    <select name="field_type[]" class="form-select">
                        <option value="VARCHAR" selected>VARCHAR</option>
                        <option value="INT">INT</option>
                        <option value="BIGINT">BIGINT</option>
                        <option value="TEXT">TEXT</option>
                        <option value="DATETIME">DATETIME</option>
                        <option value="DATE">DATE</option>
                        <option value="TINYINT">TINYINT</option>
                        <option value="DECIMAL">DECIMAL</option>
                    </select>
                </td>
                <td><input type="text" name="field_length[]" class="form-control" value="255"></td>
                <td class="text-center"><input type="checkbox" name="field_null[${rowCount}]" class="form-check-input" value="1"></td>
                <td>
                    <select name="field_key[]" class="form-select">
                        <option value="none" selected>---</option>
                        <option value="primary">PRIMARY</option>
                        <option value="unique">UNIQUE</option>
                        <option value="index">INDEX</option>
                    </select>
                </td>
                <td class="text-center"><input type="checkbox" name="field_extra[${rowCount}]" class="form-check-input" value="AUTO_INCREMENT"></td>
                <td><input type="text" name="field_comments[]" class="form-control"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><span class="icon-trash" aria-hidden="true"></span></button></td>
            `;
            tbody.appendChild(tr);
        });

        tbody.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-remove-row');
            if (btn && !btn.disabled) {
                const tr = btn.closest('tr');
                if (tr && tbody.querySelectorAll('tr').length > 1) {
                    tr.remove();
                }
            }
        });
    }
});