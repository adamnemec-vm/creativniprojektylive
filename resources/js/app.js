import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Filtry příspěvků: zaškrtnuté hodnoty pošle jako "typ=1,2" místo "typ[]=1&typ[]=2".
window.submitPostFilters = (form) => {
    const params = new URLSearchParams();
    const groups = {};

    form.querySelectorAll('input[type=checkbox][data-param]:checked').forEach((checkbox) => {
        (groups[checkbox.dataset.param] ??= []).push(checkbox.value);
    });
    Object.entries(groups).forEach(([param, ids]) => params.set(param, ids.join(',')));

    const search = form.elements.q?.value.trim();
    if (search) params.set('q', search);

    const query = params.toString().replace(/%2C/g, ',');
    window.location.href = form.action.split('#')[0] + (query ? `?${query}` : '') + '#prispevky';

    return false;
};
