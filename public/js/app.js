document.addEventListener('DOMContentLoaded', () => {
    const routeSelect = document.querySelector('[data-route-select]');

    if (routeSelect) {
        routeSelect.addEventListener('change', () => {
            const option = routeSelect.selectedOptions[0];

            if (!option) {
                return;
            }

            document.querySelector('[name="api_project_id"]').value = option.dataset.projectId || '';
            document.querySelector('[name="api_route_id"]').value = option.value || '';
            document.querySelector('[name="method"]').value = option.dataset.method || 'GET';
            document.querySelector('[name="path"]').value = option.dataset.uri || '';
        });
    }

    document.querySelectorAll('[data-history]').forEach((button) => {
        button.addEventListener('click', () => {
            document.querySelector('[name="api_project_id"]').value = button.dataset.projectId || '';
            document.querySelector('[name="api_route_id"]').value = button.dataset.routeId || '';
            document.querySelector('[name="method"]').value = button.dataset.method || 'GET';
            document.querySelector('[name="path"]').value = button.dataset.url || '';
            document.querySelector('[name="body"]').value = button.dataset.body || '';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
});
