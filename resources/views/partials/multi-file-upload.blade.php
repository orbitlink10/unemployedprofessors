@php
    $name = $name ?? 'files[]';
    $wrapperClass = trim('multi-file-upload ' . ($wrapperClass ?? ''));
    $inputClass = trim('multi-file-input ' . ($inputClass ?? ''));
    $addButtonClass = trim('multi-file-add ' . ($addButtonClass ?? ''));
    $removeButtonClass = trim('multi-file-remove ' . ($removeButtonClass ?? ''));
    $buttonText = $buttonText ?? 'Add another file';
    $required = !empty($required);
@endphp

<div class="{{ $wrapperClass }}" data-multi-file-upload data-file-required="{{ $required ? 'true' : 'false' }}">
    <div class="multi-file-list" data-file-list>
        <div class="multi-file-row" data-file-row>
            <input type="file" name="{{ $name }}" class="{{ $inputClass }}">
            <button type="button" class="{{ $removeButtonClass }}" data-file-remove>Remove</button>
        </div>
    </div>
    <button type="button" class="{{ $addButtonClass }}" data-file-add>{{ $buttonText }}</button>
</div>

@once
    <style>
        .multi-file-upload {
            display: grid;
            gap: 10px;
        }

        .multi-file-list {
            display: grid;
            gap: 8px;
        }

        .multi-file-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 8px;
            align-items: center;
        }

        .multi-file-input {
            width: 100%;
            min-width: 0;
        }

        .multi-file-add,
        .multi-file-remove {
            border: 1px solid #d8dde6;
            border-radius: 10px;
            padding: 10px 12px;
            background: #fff;
            color: #2f3236;
            font: inherit;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
        }

        .multi-file-add {
            justify-self: start;
        }

        .multi-file-add:hover,
        .multi-file-remove:hover {
            border-color: #c7cfdb;
            background: #f8fafc;
        }

        @media (max-width: 640px) {
            .multi-file-row {
                grid-template-columns: 1fr;
            }

            .multi-file-remove {
                justify-self: start;
            }
        }
    </style>
    <script>
        (() => {
            function syncWidget(widget) {
                const rows = Array.from(widget.querySelectorAll('[data-file-row]'));
                rows.forEach((row, index) => {
                    const removeButton = row.querySelector('[data-file-remove]');
                    if (removeButton) {
                        removeButton.hidden = rows.length === 1;
                        removeButton.disabled = rows.length === 1;
                    }

                    const input = row.querySelector('input[type="file"]');
                    if (input) {
                        input.setCustomValidity('');
                        if (index === 0) {
                            input.dataset.primaryFileInput = 'true';
                        } else {
                            delete input.dataset.primaryFileInput;
                        }
                    }
                });
            }

            function syncAllWidgets() {
                document.querySelectorAll('[data-multi-file-upload]').forEach(syncWidget);
            }

            document.addEventListener('click', (event) => {
                const addButton = event.target.closest('[data-file-add]');
                if (addButton) {
                    const widget = addButton.closest('[data-multi-file-upload]');
                    const list = widget ? widget.querySelector('[data-file-list]') : null;
                    const templateRow = list ? list.querySelector('[data-file-row]') : null;
                    if (!widget || !list || !templateRow) {
                        return;
                    }

                    const newRow = templateRow.cloneNode(true);
                    const input = newRow.querySelector('input[type="file"]');
                    if (input) {
                        input.value = '';
                        input.setCustomValidity('');
                    }

                    list.appendChild(newRow);
                    syncWidget(widget);
                    input?.focus();
                    return;
                }

                const removeButton = event.target.closest('[data-file-remove]');
                if (!removeButton) {
                    return;
                }

                const widget = removeButton.closest('[data-multi-file-upload]');
                const row = removeButton.closest('[data-file-row]');
                if (!widget || !row) {
                    return;
                }

                const rows = widget.querySelectorAll('[data-file-row]');
                if (rows.length <= 1) {
                    return;
                }

                row.remove();
                syncWidget(widget);
            });

            document.addEventListener('change', (event) => {
                const input = event.target.closest('[data-multi-file-upload] input[type="file"]');
                if (!input) {
                    return;
                }

                const widget = input.closest('[data-multi-file-upload]');
                widget?.querySelectorAll('input[type="file"]').forEach((fileInput) => {
                    fileInput.setCustomValidity('');
                });
            });

            document.addEventListener('submit', (event) => {
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) {
                    return;
                }

                const requiredWidgets = form.querySelectorAll('[data-multi-file-upload][data-file-required="true"]');
                for (const widget of requiredWidgets) {
                    const inputs = Array.from(widget.querySelectorAll('input[type="file"]'));
                    const hasFile = inputs.some((input) => input.files && input.files.length > 0);
                    if (hasFile) {
                        inputs.forEach((input) => input.setCustomValidity(''));
                        continue;
                    }

                    const primaryInput = inputs[0];
                    if (!primaryInput) {
                        continue;
                    }

                    event.preventDefault();
                    primaryInput.setCustomValidity('Please select at least one file.');
                    primaryInput.reportValidity();
                    break;
                }
            });

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', syncAllWidgets);
            } else {
                syncAllWidgets();
            }
        })();
    </script>
@endonce
